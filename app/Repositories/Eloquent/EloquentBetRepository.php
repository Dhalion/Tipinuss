<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BetStatus;
use App\Models\Bet;
use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentBetRepository implements BetRepositoryInterface
{
    public function findById(string $id): ?Bet
    {
        return Bet::find($id);
    }

    public function findByIdOrFail(string $id): Bet
    {
        return Bet::findOrFail($id);
    }

    public function paginateOpen(int $perPage = 15): LengthAwarePaginator
    {
        return Bet::with(['creator', 'betOptions', 'userBets'])
            ->where('status', '!=', BetStatus::Closed->value)
            ->latest()
            ->paginate($perPage);
    }

    public function save(Bet $bet): Bet
    {
        $bet->save();

        return $bet;
    }

    public function delete(Bet $bet): void
    {
        $bet->delete();
    }
}
