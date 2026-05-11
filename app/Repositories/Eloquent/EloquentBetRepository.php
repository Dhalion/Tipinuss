<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Enums\BetStatus;
use App\Models\Bet;
use App\Models\User;
use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
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
            ->where('status', '!=', BetStatus::Closed)
            ->latest()
            ->paginate($perPage);
    }

    /** @return LengthAwarePaginator<int, Bet> */
    public function paginateOpenForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $query = Bet::with(['creator', 'betOptions', 'userBets'])
            ->where('status', '!=', BetStatus::Closed);

        if (! $user->isAdmin()) {
            $query->where('organisation_id', $user->organisation_id);
        }

        return $query->latest()->paginate($perPage);
    }

    /** @return LengthAwarePaginator<int, Bet> */
    public function paginateForListing(int $perPage = 15): LengthAwarePaginator
    {
        return Bet::with(['creator', 'betOptions', 'userBets'])
            ->latest()
            ->paginate($perPage);
    }

    /** @return LengthAwarePaginator<int, Bet> */
    public function paginateForListingForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $query = Bet::with(['creator', 'betOptions', 'userBets']);

        if (! $user->isAdmin()) {
            $query->where('organisation_id', $user->organisation_id);
        }

        return $query->latest()->paginate($perPage);
    }

    public function recentOpen(int $limit = 5): Collection
    {
        return Bet::with(['creator', 'betOptions'])
            ->where('status', BetStatus::Open)
            ->latest()
            ->take($limit)
            ->get();
    }

    /** @return Collection<int, Bet> */
    public function recentOpenForUser(?User $user, int $limit = 5): Collection
    {
        $query = Bet::with(['creator', 'betOptions'])
            ->where('status', BetStatus::Open);

        if ($user !== null && ! $user->isAdmin()) {
            $query->where('organisation_id', $user->organisation_id);
        } elseif ($user === null) {
            $query->whereNull('organisation_id');
        }

        return $query->latest()->take($limit)->get();
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
