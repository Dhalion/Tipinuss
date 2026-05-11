<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\DTOs\Betting\BetOptionData;
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

    public function createForBet(Bet $bet, BetOptionData $data): BetOption
    {
        /** @var BetOption $option */
        $option = $bet->betOptions()->create([
            'title' => $data->title,
            'odds' => $data->odds,
            'base_odds' => $data->odds,
        ]);

        return $option;
    }

    public function save(BetOption $option): BetOption
    {
        $option->save();

        return $option;
    }
}
