<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

// ─── Project ───────────────────────────────────────────────────────────────

set('application', 'Tipinuss');
set('repository', 'git@github.com:Dhalion/Tipinuss.git');
set('keep_releases', 5);

// ─── Code transfer strategy: rsync from CI → server ────────────────────────

set('update_code_strategy', 'rsync');
set('rsync_src', __DIR__);
set('rsync_dest', '{{release_path}}');
add('rsync', [
    'exclude' => [
        '.env',
        '.git',
        '.gitignore',
        '.gitattributes',
        '.github',
        'node_modules',
        'vendor',
        'bootstrap/cache',
        'storage',
        'tests',
        'graphify-out',
        '.agents',
        '.opencode',
        '.direnv',
        '.phpunit.cache',
        'deploy.php',
        '*.md',
        'phpunit.xml*',
        'phpstan.neon',
        'pint.json',
        'composer.lock',
        'bun.lock',
        'package-lock.json',
    ],
]);

// ─── Shared across releases (symlinked) ─────────────────────────────────────

set('shared_files', ['.env']);
set('shared_dirs', ['storage', 'logs']);
set('writable_dirs', ['bootstrap/cache', 'storage', 'logs']);
set('writable_mode', 'chmod');
set('http_user', null);

set('bin/php', '/usr/bin/php8.4');
set('bin/composer', '/usr/local/bin/composer');

// ─── Host definitions ──────────────────────────────────────────────────────

host('production')
    ->set('hostname', getenv('DEPLOY_HOST'))
    ->set('remote_user', getenv('DEPLOY_USER'))
    ->set('port', (int) getenv('DEPLOY_PORT'))
    ->set('deploy_path', getenv('DEPLOY_PATH'))
    ->set('labels', ['env' => 'production']);

host('staging')
    ->set('hostname', getenv('STAGING_HOST'))
    ->set('remote_user', getenv('STAGING_USER'))
    ->set('port', (int) getenv('STAGING_PORT'))
    ->set('deploy_path', getenv('STAGING_PATH'))
    ->set('ssh_multiplexing', false)
    ->set('labels', ['env' => 'staging']);

// ─── Version stamp (from CI env, since .git is not on server) ─────────────

set('version_commit', getenv('GITHUB_SHA') ?: 'dev');
set('version_date', getenv('GITHUB_DATE') ?: date('Y-m-d'));

task('artisan:app:generate-version', artisan('app:generate-version --commit={{version_commit}} --date={{version_date}}'));

// ─── Hooks ─────────────────────────────────────────────────────────────────

before('deploy:symlink', 'artisan:migrate');
after('deploy:success', 'artisan:app:generate-version');
after('deploy:success', 'artisan:queue:restart');
after('deploy:failed', 'deploy:unlock');
