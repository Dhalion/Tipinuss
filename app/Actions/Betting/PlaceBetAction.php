<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\DTOs\Betting\PlaceBetData;
use App\Enums\UserBetStatus;
use App\Models\UserBet;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Services\Betting\BetCalculationService;
use App\Services\Betting\BettingValidationService;
use App\Services\User\UserBalanceService;
use Illuminate\Support\Facades\DB;

final class PlaceBetAction
{
    public function __construct(
        private BettingValidationService $validation,
        private BetCalculationService $calculation,
        private UserBalanceService $balance,
        private UserBetRepositoryInterface $userBets,
    ) {}

    public function execute(PlaceBetData $data): UserBet
    {
        $this->validation->validateBetIsOpen($data->option->bet->isOpen());
        $this->validation->validateAmountWithinBounds($data->amount);
        $this->validation->validateBalanceSufficient($data->user, $data->amount);

        $potentialWinnings = $this->calculation->calculatePotentialWinnings($data->option, $data->amount);

        return DB::transaction(function () use ($data, $potentialWinnings): UserBet {
            $this->balance->decrementBalance($data->user, $data->amount);

            $userBet = new UserBet([
                'user_id' => $data->user->id,
                'bet_option_id' => $data->option->id,
                'amount_wagered' => $data->amount,
                'potential_winnings' => $potentialWinnings,
                'status' => UserBetStatus::Pending,
            ]);

            return $this->userBets->save($userBet);
        });
    }
}
