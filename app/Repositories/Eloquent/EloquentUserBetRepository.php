<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentUserBetRepository implements UserBetRepositoryInterface
{
    public function findByOption(BetOption $option): Collection
    {
        return $option->userBets()->with('user')->get();
    }

    public function recentForBet(Bet $bet, int $limit = 20): Collection
    {
        return $bet->userBets()
            ->with(['user', 'betOption'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function recentForUser(User $user, int $limit = 10): Collection
    {
        return $user->userBets()
            ->with(['betOption.bet.creator'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * @param  array<int, string>  $ids
     * @return Collection<int, UserBet>
     */
    public function findByIdsWithOptionAndBet(array $ids): Collection
    {
        return UserBet::with('betOption.bet')
            ->whereIn('id', $ids)
            ->get();
    }

    public function countDistinctBettorsForBet(Bet $bet): int
    {
        return $bet->userBets()
            ->distinct('user_id')
            ->count('user_id');
    }

    public function save(UserBet $userBet): UserBet
    {
        $userBet->save();

        return $userBet;
    }
}
