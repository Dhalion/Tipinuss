<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Constants\AppDefaults;
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
    public function recentForBet(Bet $bet, int $limit = AppDefaults::RECENT_BETS_LIMIT): Collection;

    /** @return Collection<int, UserBet> */
    public function recentForUser(User $user, int $limit = AppDefaults::RECENT_USER_BETS_LIMIT): Collection;

    public function findForUserAndBet(User $user, Bet $bet): ?UserBet;

    public function countDistinctBettorsForBet(Bet $bet): int;

    /**
     * @param  array<int, string>  $ids
     * @return Collection<int, UserBet>
     */
    public function findByIdsWithOptionAndBet(array $ids): Collection;

    /**
     * @param  array<int, string>  $optionIds
     * @return Collection<int, UserBet>
     */
    public function findByOptionIds(array $optionIds): Collection;

    public function countForUser(User $user): int;

    public function countForUserByStatus(User $user, string $status): int;

    public function save(UserBet $userBet): UserBet;
}
