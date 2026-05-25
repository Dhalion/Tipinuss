<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\DTOs\Betting\CloseBetData;
use App\Enums\BetStatus;
use App\Enums\TransactionType;
use App\Enums\UserBetStatus;
use App\Exceptions\BetException;
use App\Models\Bet;
use App\Models\User;
use App\Models\UserBet;
use App\Events\Betting\BetClosed;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Services\User\BalanceTransactionService;
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
        private BalanceTransactionService $balanceTransactions,
    ) {}

    public function execute(CloseBetData $data): Bet
    {
        Gate::authorize('closeBet', $data->bet);

        $winningOption = $this->betOptions->findByIdOrFail($data->winningOptionId);

        if ($winningOption->bet_id !== $data->bet->id) {
            throw BetException::wrongBetOption();
        }

        $participants = [];

        $bet = DB::transaction(function () use ($data, $winningOption, &$participants): Bet {
            $winningUserBets = $this->userBets->findByOption($winningOption);

            $winningUserBets->each(function (UserBet $userBet) use ($winningOption, &$participants): void {
                $winner = $userBet->user;
                if ($winner instanceof User) {
                    $winnings = (int) round($userBet->amount_wagered * $winningOption->odds);
                    $this->balance->incrementBalance($winner, $winnings);

                    $this->balanceTransactions->log(
                        user: $winner,
                        type: TransactionType::BetWon,
                        amount: $winnings,
                        balanceAfter: $winner->soapnuts,
                        userBetId: $userBet->id,
                        description: $userBet->betOption->bet->title.' — '.$userBet->betOption->title,
                    );
                }
                $userBet->status = UserBetStatus::Won;
                $this->userBets->save($userBet);

                $participants[] = [
                    'user' => $userBet->user,
                    'status' => UserBetStatus::Won->value,
                    'wagered' => $userBet->amount_wagered,
                    'winnings' => $userBet->potential_winnings,
                ];
            });

            $losingOptions = $data->bet->betOptions->reject(fn ($o) => $o->id === $winningOption->id);

            $losingUserBetIds = $losingOptions->flatMap(fn ($option) => $option->userBets->pluck('id'));

            if ($losingUserBetIds->isNotEmpty()) {
                $losingUserBets = $this->userBets->findByIdsWithOptionAndBet($losingUserBetIds->all());

                $losingUserBets->each(function (UserBet $userBet) use (&$participants): void {
                    $userBet->status = UserBetStatus::Lost;
                    $this->userBets->save($userBet);

                    $participants[] = [
                        'user' => $userBet->user,
                        'status' => UserBetStatus::Lost->value,
                        'wagered' => $userBet->amount_wagered,
                        'winnings' => $userBet->potential_winnings,
                    ];
                });
            }

            foreach ($losingOptions as $option) {
                $option->result = false;
                $this->betOptions->save($option);
            }

            $winningOption->result = true;
            $this->betOptions->save($winningOption);

            $data->bet->status = BetStatus::Closed;

            return $this->bets->save($data->bet);
        });

        event(new BetClosed(bet: $bet, participants: $participants));

        return $bet;
    }
}
