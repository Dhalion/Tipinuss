<?php

declare(strict_types=1);

namespace App\Services\Betting;

use App\Exceptions\BetException;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;

final class BettingValidationService
{
    public const int MIN_BET_AMOUNT = 1;

    public const int MAX_BET_AMOUNT = 100_000;

    public function validateOptionExists(?BetOption $option): void
    {
        if ($option === null) {
            throw BetException::optionNotFound();
        }
    }

    public function validateBetIsOpen(Bet $bet): void
    {
        if (! $bet->isOpen()) {
            throw BetException::betAlreadyClosed();
        }

        if ($bet->isExpired()) {
            throw BetException::betExpired();
        }
    }

    public function validateBalanceSufficient(User $user, int $requiredAmount): void
    {
        if ($user->soapnuts < $requiredAmount) {
            $shortfall = $requiredAmount - $user->soapnuts;
            throw BetException::insufficientBalance($shortfall);
        }
    }

    public function canCloseBet(Bet $bet, User $user, int $distinctBettorCount): bool
    {
        if (! $bet->isOpen()) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $distinctBettorCount >= 2;
    }

    public function validateAmountWithinBounds(
        int $amount,
        int $minAmount = self::MIN_BET_AMOUNT,
        int $maxAmount = self::MAX_BET_AMOUNT,
    ): void {
        if ($amount < $minAmount || $amount > $maxAmount) {
            throw BetException::amountOutOfBounds($minAmount, $maxAmount);
        }
    }
}
