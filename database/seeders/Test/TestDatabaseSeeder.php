<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use Illuminate\Database\Seeder;

final class TestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BetaKeySeeder::class,
            BetSeeder::class,
        ]);
    }
}
