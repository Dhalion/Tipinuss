<?php

declare(strict_types=1);

namespace App\Services\Betting;

use App\Models\BetOption;

class BetCalculationService
{
    public function calculatePotentialWinnings(BetOption $option, int $amountWagered): int
    {
        return (int) ($option->odds * $amountWagered);
    }

    public function calculateRequiredBalance(int $amountWagered): int
    {
        return $amountWagered;
    }
}
