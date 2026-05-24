<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

final class DeleteUserCommand extends Command
{
    protected $signature = 'tipinuss:user:delete {email : The email address of the user to delete}';

    protected $description = 'Permanently delete a user account';

    public function __construct(private UserRepositoryInterface $users)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $user = $this->users->findByEmail($this->argument('email'));

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        $this->line("Name:      {$user->name}");
        $this->line("Email:     {$user->email}");
        $this->line("Soapnuts:  {$user->soapnuts}");
        $this->line('Admin:     '.($user->isAdmin() ? 'yes' : 'no'));

        if (! $this->confirm('Permanently delete this user? This cannot be undone.')) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $this->users->delete($user);

        $this->info("User '{$user->email}' deleted.");

        return self::SUCCESS;
    }
}
