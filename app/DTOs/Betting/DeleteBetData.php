<?php

declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\Bet;

final readonly class DeleteBetData
{
    public function __construct(
        public Bet $bet,
        public bool $refund = false,
    ) {}
}
