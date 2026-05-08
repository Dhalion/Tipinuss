<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Bet;
use App\Models\BetOption;
use App\Repositories\Contracts\BetOptionRepositoryInterface;

final class EloquentBetOptionRepository implements BetOptionRepositoryInterface
{
    public function findById(string $id): ?BetOption
    {
        return BetOption::find($id);
    }

    public function findByIdOrFail(string $id): BetOption
    {
        return BetOption::findOrFail($id);
    }

    public function createForBet(Bet $bet, array $attributes): BetOption
    {
        /** @var BetOption $option */
        $option = $bet->betOptions()->create($attributes);

        return $option;
    }
}
