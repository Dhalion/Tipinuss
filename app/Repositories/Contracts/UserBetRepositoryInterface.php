<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Database\Eloquent\Collection;

interface UserBetRepositoryInterface
{
    /** @return Collection<int, UserBet> */
    public function findByOption(BetOption $option): Collection;

    /** @return Collection<int, UserBet> */
    public function recentForBet(Bet $bet, int $limit = 20): Collection;

    /** @return Collection<int, UserBet> */
    public function recentForUser(User $user, int $limit = 10): Collection;

    public function countDistinctBettorsForBet(Bet $bet): int;

    public function save(UserBet $userBet): UserBet;
}
