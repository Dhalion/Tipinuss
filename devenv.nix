{ pkgs, lib, config, inputs, ... }:

{
  dotenv.enable = true;

  packages = [ pkgs.git pkgs.yarn pkgs.php84 pkgs.bun ];

  languages.javascript = {
    enable = lib.mkDefault true;
    package = lib.mkDefault pkgs.nodejs_24;
  };
  

  languages.php = {
    enable = lib.mkDefault true;
    version = lib.mkDefault "8.4";
    extensions = [ "grpc" "imagick" "xdebug" ];

    ini = ''
      memory_limit = 2G
      realpath_cache_ttl = 3600
      session.gc_probability = 0
      ${lib.optionalString config.services.redis.enable ''
      session.save_handler = redis
      session.save_path = "tcp://127.0.0.1:${toString config.services.redis.port}/0"
      ''}
      display_errors = On
      error_reporting = E_ALL
      opcache.memory_consumption = 256M
      opcache.interned_strings_buffer = 20
      zend.assertions = 0
      short_open_tag = 0
      zend.detect_unicode = 0
      realpath_cache_ttl = 3600
      post_max_size = 32M
      upload_max_filesize = 32M
    '';

    fpm.pools.web = lib.mkDefault {
      settings = {
        "clear_env" = "no";
        "pm" = "dynamic";
        "pm.max_children" = 10;
        "pm.start_servers" = 2;
        "pm.min_spare_servers" = 1;
        "pm.max_spare_servers" = 10;
      };
    };
  };

  services.caddy = {
    enable = lib.mkDefault true;

    virtualHosts.":8080" = lib.mkDefault {
      extraConfig = lib.mkDefault ''
        root * public
        php_fastcgi unix/${config.languages.php.fpm.pools.web.socket} {
            trusted_proxies private_ranges
        }
        file_server
      '';
    };
  };

  services.mysql = {
    enable = true;
    package = pkgs.mysql84;
    initialDatabases = lib.mkDefault [{ name = "laravel"; }];
    ensureUsers = lib.mkDefault [
      {
        name = "laravel";
        password = "laravel";
        ensurePermissions = {
          "laravel.*" = "ALL PRIVILEGES";
          "laravel_test.*" = "ALL PRIVILEGES";
        };
      }
    ];
    settings = {
      mysqld = {
        group_concat_max_len = 320000;
        log_bin_trust_function_creators = 1;
        sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION";
      };
    };
  };
}
