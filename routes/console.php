<?php

declare(strict_types=1);

use App\Models\BetaAccessKey;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function (): void {
    BetaAccessKey::query()
        ->where('is_active', true)
        ->where('expires_at', '<', now())
        ->update(['is_active' => false]);
})->daily()->description('Deactivate expired beta access keys');

Schedule::call(function (): void {
    app('db.connection')->table('notifications')
        ->whereNotNull('read_at')
        ->where('read_at', '<', now()->subDays(30))
        ->delete();
})->daily()->description('Clean up read notifications older than 30 days');

Schedule::command('model:prune', [
    '--model' => [BetaAccessKey::class],
])->daily()->description('Prune expired/unused models');
