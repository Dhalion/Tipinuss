<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

final class ChangePasswordCommand extends Command
{
    protected $signature = 'tipinuss:user:change-password {email : User email} {password : New password (min 8 characters)}';

    protected $description = 'Change the password for a user account';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        $password = $this->argument('password');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');

            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("✓ Password updated for {$user->email}.");

        return self::SUCCESS;
    }
}
