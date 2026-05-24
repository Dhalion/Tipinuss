<?php

declare(strict_types=1);

namespace App\DTOs\Betting;

use App\Models\User;
use Carbon\CarbonImmutable;

final readonly class CreateBetData
{
    /**
     * @param  array<int, BetOptionData>  $options
     */
    public function __construct(
        public User $creator,
        public string $title,
        public ?string $description,
        public ?CarbonImmutable $expiresAt,
        public array $options,
        public ?string $organisationId,
    ) {}
}
