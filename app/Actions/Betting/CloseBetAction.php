<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\DTOs\Betting\CloseBetData;
use App\Enums\BetStatus;
use App\Enums\UserBetStatus;
use App\Exceptions\BetException;
use App\Models\Bet;
use App\Models\User;
use App\Models\UserBet;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Services\User\UserBalanceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class CloseBetAction
{
    public function __construct(
        private BetOptionRepositoryInterface $betOptions,
        private BetRepositoryInterface $bets,
        private UserBetRepositoryInterface $userBets,
        private UserBalanceService $balance,
    ) {}

    public function execute(CloseBetData $data): Bet
    {
        Gate::authorize('closeBet', $data->bet);

        $winningOption = $this->betOptions->findByIdOrFail($data->winningOptionId);

        if ($winningOption->bet_id !== $data->bet->id) {
            throw new BetException('Winning option does not belong to this bet.');
        }

        return DB::transaction(function () use ($data, $winningOption): Bet {
            $winningUserBets = $this->userBets->findByOption($winningOption);

            $winningUserBets->each(function (UserBet $userBet) use ($winningOption): void {
                $winner = $userBet->user;
                if ($winner instanceof User) {
                    $winnings = (int) round($userBet->amount_wagered * $winningOption->odds);
                    $this->balance->incrementBalance($winner, $winnings);
                }
                $userBet->status = UserBetStatus::Won;
                $this->userBets->save($userBet);
            });

            foreach ($data->bet->betOptions as $option) {
                if ($option->id === $winningOption->id) {
                    $option->result = true;
                } else {
                    $option->result = false;
                    $this->userBets->findByOption($option)->each(function (UserBet $userBet): void {
                        $userBet->status = UserBetStatus::Lost;
                        $this->userBets->save($userBet);
                    });
                }
                $this->betOptions->save($option);
            }

            $data->bet->status = BetStatus::Closed;

            return $this->bets->save($data->bet);
        });
    }
}
