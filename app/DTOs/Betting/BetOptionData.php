<?php

declare(strict_types=1);

namespace App\DTOs\Betting;

final class BetOptionData
{
    public function __construct(
        public readonly string $title,
        public readonly float $odds,
    ) {}

    public static function make(string $title, float $odds): self
    {
        return new self(title: $title, odds: $odds);
    }
}
