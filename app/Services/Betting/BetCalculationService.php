<?php

declare(strict_types=1);

namespace App\Services\Betting;

use App\Models\BetOption;

final class BetCalculationService
{
    public function calculatePotentialWinnings(BetOption $option, int $amountWagered): int
    {
        return (int) round($option->odds * $amountWagered);
    }
}
