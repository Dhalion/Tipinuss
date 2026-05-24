<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

final class ListUsersCommand extends Command
{
    protected $signature = 'tipinuss:user:list';

    protected $description = 'List all users with their balance, admin status and organisation';

    public function __construct(private UserRepositoryInterface $users)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $users = $this->users->allWithBetCount();

        if ($users->isEmpty()) {
            $this->info('No users found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Name', 'Email', 'Soapnuts', 'Admin', 'Organisation', 'Bets', 'Registered'],
            $users->map(fn (User $user) => [
                $user->name,
                $user->email,
                number_format($user->soapnuts),
                $user->isAdmin() ? 'yes' : 'no',
                $user->organisation?->name ?? '—',
                $user->user_bets_count,
                $user->created_at?->format('Y-m-d'),
            ]),
        );

        return self::SUCCESS;
    }
}
