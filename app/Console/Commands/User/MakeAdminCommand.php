<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

final class MakeAdminCommand extends Command
{
    protected $signature = 'tipinuss:user:make-admin {email : The email address of the user}';

    protected $description = 'Grant admin privileges to a user';

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

        if ($user->isAdmin()) {
            $this->warn("{$user->email} is already an admin.");

            return self::SUCCESS;
        }

        $user->is_admin = true;
        $this->users->save($user);

        $this->info("{$user->email} is now an admin.");

        return self::SUCCESS;
    }
}
