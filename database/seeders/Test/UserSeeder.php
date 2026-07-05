<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'is_admin' => true,
            'is_approved' => true,
            'soapnuts' => 9999,
        ]);

        User::create([
            'name' => 'Alice',
            'email' => 'alice@test.com',
            'password' => 'password',
            'is_admin' => false,
            'is_approved' => true,
            'soapnuts' => 500,
        ]);

        User::create([
            'name' => 'Bob',
            'email' => 'bob@test.com',
            'password' => 'password',
            'is_admin' => false,
            'is_approved' => false,
            'soapnuts' => 0,
        ]);

        Organisation::create(['name' => 'Standard-Organisation']);
    }
}
