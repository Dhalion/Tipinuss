<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use App\Services\Betting\BetCalculationService;
use App\Services\Betting\BettingValidationService;
use App\Services\User\UserBalanceService;

class PlaceBetAction
{
    public function __construct(
        private BettingValidationService $validation,
        private BetCalculationService $calculation,
        private UserBalanceService $balance,
    ) {}

    public function execute(User $user, BetOption|null $option, int $amount): UserBet
    {
        $this->validation->validateOptionExists($option);

        if ($option === null) {
            throw new \LogicException('Option was validated to exist but is null');
        }

        $this->validation->validateBetIsOpen($option->bet->isOpen());
        $this->validation->validateAmountWithinBounds($amount);
        $this->validation->validateBalanceSufficient($user, $amount);

        $potentialWinnings = $this->calculation->calculatePotentialWinnings($option, $amount);

        $this->balance->decrementBalance($user, $amount);

        return UserBet::create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
            'amount_wagered' => $amount,
            'potential_winnings' => $potentialWinnings,
            'status' => 'pending',
        ]);
    }
}

