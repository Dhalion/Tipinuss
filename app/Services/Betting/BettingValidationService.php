<?php

declare(strict_types=1);

namespace App\Services\Betting;

use App\Exceptions\BetException;
use App\Models\BetOption;
use App\Models\User;

class BettingValidationService
{
    public function validateOptionExists(BetOption|null $option): void
    {
        if ($option === null) {
            throw new BetException('Betting option not found.');
        }
    }

    public function validateBetIsOpen(bool $betIsOpen): void
    {
        if (!$betIsOpen) {
            throw new BetException('Bet is already closed and cannot accept new bets.');
        }
    }

    public function validateBalanceSufficient(User $user, int $requiredAmount): void
    {
        if ($user->soapnuts < $requiredAmount) {
            $shortfall = $requiredAmount - $user->soapnuts;
            throw new BetException("Insufficient balance. You need {$shortfall} more soapnuts.");
        }
    }

    public function validateAmountWithinBounds(int $amount, int $minAmount = 1, int $maxAmount = 100000): void
    {
        if ($amount < $minAmount || $amount > $maxAmount) {
            throw new BetException("Bet amount must be between {$minAmount} and {$maxAmount}.");
        }
    }
}
