<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Exceptions\BetException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

final class UserBalanceService
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function decrementBalance(User $user, int $amount): void
    {
        if ($user->soapnuts < $amount) {
            throw BetException::insufficientBalance($amount - $user->soapnuts);
        }

        $this->users->adjustBalance($user, -$amount);
    }

    public function incrementBalance(User $user, int $amount): void
    {
        $this->users->adjustBalance($user, $amount);
    }

    public function getAvailableBalance(User $user): int
    {
        $freshUser = $this->users->findById($user->id);

        return $freshUser !== null ? $freshUser->soapnuts : $user->soapnuts;
    }
}
