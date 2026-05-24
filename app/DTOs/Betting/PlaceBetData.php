<?php

declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\BetOption;
use App\Models\User;

final readonly class PlaceBetData
{
    public function __construct(
        public User $user,
        public BetOption $option,
        public int $amount,
    ) {}
}
