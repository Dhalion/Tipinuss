<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

final class RevokeAdminCommand extends Command
{
    protected $signature = 'tipinuss:user:revoke-admin {email : The email address of the user}';

    protected $description = 'Revoke admin privileges from a user';

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

        if (! $user->isAdmin()) {
            $this->warn("{$user->email} is not an admin.");

            return self::SUCCESS;
        }

        $user->is_admin = false;
        $this->users->save($user);

        $this->info("Admin privileges revoked from {$user->email}.");

        return self::SUCCESS;
    }
}
