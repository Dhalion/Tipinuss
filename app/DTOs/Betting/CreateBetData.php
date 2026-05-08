<?php

declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\User;
use Carbon\CarbonImmutable;

final class CreateBetData
{
    /**
     * @param  array<int, BetOptionData>  $options
     */
    public function __construct(
        public readonly User $creator,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?CarbonImmutable $expiresAt,
        public readonly array $options,
    ) {}

    /**
     * @param  array<int, BetOptionData>  $options
     */
    public static function make(
        User $creator,
        string $title,
        ?string $description,
        ?CarbonImmutable $expiresAt,
        array $options,
    ): self {
        return new self(
            creator: $creator,
            title: $title,
            description: $description,
            expiresAt: $expiresAt,
            options: $options,
        );
    }
}
