<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Exceptions\BetException;
use App\Models\User;

class UserBalanceService
{
    public function decrementBalance(User $user, int $amount): void
    {
        $decremented = $user->decrement('soapnuts', $amount);

        if ($decremented === 0) {
            throw new BetException('Failed to decrement user balance.');
        }
    }

    public function incrementBalance(User $user, int $amount): void
    {
        $user->increment('soapnuts', $amount);
    }

    public function getAvailableBalance(User $user): int
    {
        return $user->fresh()->soapnuts;
    }
}
