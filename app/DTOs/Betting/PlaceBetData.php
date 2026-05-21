<?php

declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\BetOption;
use App\Models\User;

final readonly class PlaceBetData
{
    public function __construct(
        public readonly User $user,
        public readonly BetOption $option,
        public readonly int $amount,
    ) {}

    public static function make(User $user, BetOption $option, int $amount): self
    {
        return new self(user: $user, option: $option, amount: $amount);
    }
}
