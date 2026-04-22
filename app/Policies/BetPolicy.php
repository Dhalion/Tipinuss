<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Bet;
use App\Models\BetStatus;
use App\Models\User;

class BetPolicy
{
    public function closeBet(User $user, Bet $bet): bool
    {
        return $user->id === $bet->creator_id && $bet->status === BetStatus::Open;
    }

    public function deleteBet(User $user, Bet $bet): bool
    {
        return $user->id === $bet->creator_id && $bet->status === BetStatus::Open;
    }

    public function viewBet(User $user, Bet $bet): bool
    {
        return true;
    }
}
