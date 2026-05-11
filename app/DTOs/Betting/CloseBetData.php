<?php

declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\Bet;

final readonly class CloseBetData
{
    public function __construct(
        public Bet $bet,
        public string $winningOptionId,
    ) {}

    public static function make(Bet $bet, string $winningOptionId): self
    {
        return new self(bet: $bet, winningOptionId: $winningOptionId);
    }
}
