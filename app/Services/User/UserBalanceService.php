<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Exceptions\BetException;
use App\Models\User;

final class UserBalanceService
{
    public function decrementBalance(User $user, int $amount): void
    {
        if ($user->soapnuts < $amount) {
            throw BetException::insufficientBalance($amount - $user->soapnuts);
        }

        $user->decrement('soapnuts', $amount);
    }

    public function incrementBalance(User $user, int $amount): void
    {
        $user->increment('soapnuts', $amount);
    }

    public function getAvailableBalance(User $user): int
    {
        $freshUser = $user->fresh();

        return $freshUser instanceof User ? $freshUser->soapnuts : $user->soapnuts;
    }
}
