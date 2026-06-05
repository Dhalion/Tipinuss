<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/rsync.php';

set('application', 'Tipinuss');
set('keep_releases', 5);

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
        'bun.lock',
        'package-lock.json',
        'inventory.local.php',
    ],
]);

set('shared_files', ['.env']);
set('shared_dirs', ['storage']);
set('writable_dirs', ['bootstrap/cache']);
set('writable_mode', 'chmod');
set('writable_chmod_mode', '2775');
set('http_user', null);

set('bin/php', '/usr/bin/php8.4');
set('bin/composer', '/usr/local/bin/composer');

host('production')
    ->setHostname(getenv('DEPLOY_HOST'))
    ->setRemoteUser(getenv('DEPLOY_USER'))
    ->setPort((int) getenv('DEPLOY_PORT'))
    ->setDeployPath(getenv('DEPLOY_PATH'))
    ->setLabels(['env' => 'production', 'stage' => 'production']);

host('staging')
    ->setHostname(getenv('DEPLOY_HOST'))
    ->setRemoteUser(getenv('DEPLOY_USER'))
    ->setPort((int) getenv('DEPLOY_PORT'))
    ->setDeployPath(getenv('DEPLOY_PATH'))
    ->setLabels(['env' => 'staging', 'stage' => 'staging']);

set('version_commit', getenv('GITHUB_SHA') ?: 'dev');
set('version_date', getenv('GITHUB_DATE') ?: date('Y-m-d'));

task('artisan:app:generate-version', function () {
    run('{{bin/php}} {{bin/artisan}} app:generate-version --commit={{version_commit}} --date={{version_date}}');
});

after('deploy:symlink', 'artisan:app:generate-version');
after('deploy:symlink', 'artisan:queue:restart');
after('deploy:failed', 'deploy:unlock');

task('deploy:prepare', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:env',
    'deploy:shared',
    'deploy:writable',
]);

task('deploy', [
    'deploy:prepare',
    'artisan:storage:link',
    'artisan:optimize',
    'artisan:migrate',
    'deploy:publish',
    'artisan:reload',
]);
