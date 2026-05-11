<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Betting\BetOptionData;
use App\Models\Bet;
use App\Models\BetOption;

interface BetOptionRepositoryInterface
{
    public function findById(string $id): ?BetOption;

    public function findByIdOrFail(string $id): BetOption;

    public function createForBet(Bet $bet, BetOptionData $data): BetOption;

    public function save(BetOption $option): BetOption;
}
