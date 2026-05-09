<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;

final class MakeAdminCommand extends Command
{
    protected $signature = 'tipinuss:user:make-admin {email : The email address of the user}';

    protected $description = 'Grant admin privileges to a user';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        if ($user->isAdmin()) {
            $this->warn("{$user->email} is already an admin.");

            return self::SUCCESS;
        }

        $user->is_admin = true;
        $user->save();

        $this->info("✓ {$user->email} is now an admin.");

        return self::SUCCESS;
    }
}
