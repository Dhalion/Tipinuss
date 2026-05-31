<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/laravel.php';

// ─── Project ───────────────────────────────────────────────────────────────

set('application', 'Tipinuss');
set('repository', 'git@github.com:Dhalion/Tipinuss.git');
set('keep_releases', 5);

// ─── Code transfer strategy: rsync from CI → server ────────────────────────
// Node_modules, vendor, .git etc. are built on the server or excluded.
// Vite assets are pre-built in CI (public/build/ travels with rsync).

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

// HTTP user – the web server runs as 'caddy' on production.
// Leave null when writable_mode = chmod (no chown needed).
set('http_user', null);

set('bin/php', '/usr/bin/php8.4');
set('bin/composer', '/usr/local/bin/composer');

// ─── Host definitions ──────────────────────────────────────────────────────
// All values via environment variables (secrets) – never hardcoded.

host('production')
    ->setHostname(getenv('DEPLOY_HOST'))
    ->setRemoteUser(getenv('DEPLOY_USER'))
    ->setPort((int) (getenv('DEPLOY_PORT') ?: 22))
    ->setDeployPath(getenv('DEPLOY_PATH'))
    ->setLabels(['env' => 'production']);

host('staging')
    ->setHostname(getenv('STAGING_HOST'))
    ->setRemoteUser(getenv('STAGING_USER'))
    ->setPort((int) (getenv('STAGING_PORT') ?: 22))
    ->setDeployPath(getenv('STAGING_PATH'))
    ->setSshMultiplexing(false)
    ->setLabels(['env' => 'staging']);

// ─── Version stamp (from CI env, since .git is not on server) ─────────────

set('version_commit', getenv('GITHUB_SHA') ?: 'dev');
set('version_date', getenv('GITHUB_DATE') ?: date('Y-m-d'));

task('artisan:app:generate-version', artisan('app:generate-version --commit={{version_commit}} --date={{version_date}}'));

// ─── Hooks ─────────────────────────────────────────────────────────────────

// Run migrations BEFORE symlink switch (old code still serves requests
// and is compatible with additive migrations).
before('deploy:symlink', 'artisan:migrate');

// Write commit version stamp after successful deploy.
after('deploy:success', 'artisan:app:generate-version');

// If queue workers run on the server, signal them to restart.
after('deploy:success', 'artisan:queue:restart');

// Clean up lock on failure so next deploy can proceed.
after('deploy:failed', 'deploy:unlock');
