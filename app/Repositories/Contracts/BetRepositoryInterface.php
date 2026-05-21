<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BetRepositoryInterface
{
    public function findById(string $id): ?Bet;

    public function findByIdOrFail(string $id): Bet;

    /** @return LengthAwarePaginator<Bet> */
    public function paginateOpen(int $perPage = 15): LengthAwarePaginator;

    /** @return LengthAwarePaginator<int, Bet> */
    public function paginateOpenForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    /** @return LengthAwarePaginator<int, Bet> */
    public function paginateForListing(int $perPage = 15): LengthAwarePaginator;

    /** @return LengthAwarePaginator<int, Bet> */
    public function paginateForListingForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    /** @return Collection<int, Bet> */
    public function recentOpen(int $limit = 5): Collection;

    /** @return Collection<int, Bet> */
    public function recentOpenForUser(?User $user, int $limit = 5): Collection;

    public function existsBySlug(string $slug): bool;

    public function save(Bet $bet): Bet;

    public function delete(Bet $bet): void;
}
