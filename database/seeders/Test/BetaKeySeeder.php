<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Models\BetaAccessKey;
use App\Models\Organisation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class BetaKeySeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $admin */
        $admin = User::where('email', 'admin@test.com')->first();

        $orgA = Organisation::create(['name' => 'Org A']);
        $orgB = Organisation::create(['name' => 'Org B']);

        BetaAccessKey::create([
            'key' => 'ABCD-1234',
            'organisation_id' => $orgA->id,
            'created_by_user_id' => $admin->id,
            'is_active' => true,
            'expires_at' => Carbon::now()->addYear(),
        ]);

        BetaAccessKey::create([
            'key' => 'EFGH-5678',
            'organisation_id' => $orgB->id,
            'created_by_user_id' => $admin->id,
            'is_active' => true,
            'expires_at' => Carbon::now()->addYear(),
        ]);

        BetaAccessKey::create([
            'key' => 'EXPIRED-KEY',
            'organisation_id' => $orgA->id,
            'created_by_user_id' => $admin->id,
            'is_active' => true,
            'expires_at' => Carbon::now()->subDay(),
        ]);

        BetaAccessKey::create([
            'key' => 'INACTIVE-KEY',
            'organisation_id' => $orgA->id,
            'created_by_user_id' => $admin->id,
            'is_active' => false,
            'expires_at' => Carbon::now()->addYear(),
        ]);
    }
}
