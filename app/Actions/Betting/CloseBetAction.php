<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\Enums\BetStatus;
use App\Exceptions\BetException;
use App\Models\Bet;
use App\Models\User;
use App\Models\UserBet;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Services\User\UserBalanceService;
use Illuminate\Support\Facades\DB;

final class CloseBetAction
{
    public function __construct(
        private BetOptionRepositoryInterface $betOptions,
        private BetRepositoryInterface $bets,
        private UserBetRepositoryInterface $userBets,
        private UserBalanceService $balance,
    ) {}

    public function execute(Bet $bet, string $winningOptionId): Bet
    {
        $winningOption = $this->betOptions->findByIdOrFail($winningOptionId);

        if ($winningOption->bet_id !== $bet->id) {
            throw new BetException('Winning option does not belong to this bet.');
        }

        return DB::transaction(function () use ($bet, $winningOption): Bet {
            $winningUserBets = $this->userBets->findByOption($winningOption);

            $winningUserBets->each(function (UserBet $userBet) use ($winningOption): void {
                $winner = $userBet->user;
                if ($winner instanceof User) {
                    $winnings = (int) round($userBet->amount_wagered * $winningOption->odds);
                    $this->balance->incrementBalance($winner, $winnings);
                }
            });

            $bet->status = BetStatus::Closed;

            return $this->bets->save($bet);
        });
    }
}
