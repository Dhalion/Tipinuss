<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Betting\BetOptionData;
use App\Models\Bet;
use App\Models\BetOption;
use Illuminate\Support\Collection;

interface BetOptionRepositoryInterface
{
    public function findById(string $id): ?BetOption;

    public function findByIdOrFail(string $id): BetOption;

    /** @return Collection<string, string> */
    public function getOptionIdsByBetId(string $betId): Collection;

    public function createForBet(Bet $bet, BetOptionData $data): BetOption;

    public function save(BetOption $option): BetOption;
}
