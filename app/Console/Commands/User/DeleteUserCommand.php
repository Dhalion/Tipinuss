<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Models\User;
use Illuminate\Console\Command;

final class DeleteUserCommand extends Command
{
    protected $signature = 'tipinuss:user:delete {email : The email address of the user to delete}';

    protected $description = 'Permanently delete a user account';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        $this->line("Name:      {$user->name}");
        $this->line("Email:     {$user->email}");
        $this->line("Soapnuts:  {$user->soapnuts} 🌰");
        $this->line('Admin:     '.($user->isAdmin() ? 'yes' : 'no'));

        if (! $this->confirm('Permanently delete this user? This cannot be undone.')) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $user->delete();

        $this->info("✓ User '{$user->email}' deleted.");

        return self::SUCCESS;
    }
}
