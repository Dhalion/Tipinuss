<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;

final class RevokeAdminCommand extends Command
{
    protected $signature = 'tipinuss:user:revoke-admin {email : The email address of the user}';

    protected $description = 'Revoke admin privileges from a user';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        if (! $user->isAdmin()) {
            $this->warn("{$user->email} is not an admin.");

            return self::SUCCESS;
        }

        $user->is_admin = false;
        $user->save();

        $this->info("✓ Admin privileges revoked from {$user->email}.");

        return self::SUCCESS;
    }
}
