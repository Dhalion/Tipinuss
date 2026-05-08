<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bet;
use App\Models\BetOption;

interface BetOptionRepositoryInterface
{
    public function findById(string $id): ?BetOption;

    public function findByIdOrFail(string $id): BetOption;

    /** @param array<string, mixed> $attributes */
    public function createForBet(Bet $bet, array $attributes): BetOption;
}
