<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Bet;
use App\Models\User;

final class BetPolicy
{
    public function closeBet(User $user, Bet $bet): bool
    {
        return $this->isOpenAndAuthorizedFor($user, $bet);
    }

    public function deleteBet(User $user, Bet $bet): bool
    {
        return $this->isOpenAndAuthorizedFor($user, $bet);
    }

    public function viewBet(User $user, Bet $bet): bool
    {
        return true;
    }

    private function isOpenAndAuthorizedFor(User $user, Bet $bet): bool
    {
        if (! $bet->isOpen()) {
            return false;
        }

        return $user->isAdmin() || $user->id === $bet->user_id;
    }
}
