<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface BetRepositoryInterface
{
    public function findById(string $id): ?Bet;

    public function findByIdOrFail(string $id): Bet;

    /** @return LengthAwarePaginator<Bet> */
    public function paginateOpen(int $perPage = 15): LengthAwarePaginator;

    /** @return LengthAwarePaginator<int, Bet> */
    public function paginateOpenForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    public function save(Bet $bet): Bet;

    public function delete(Bet $bet): void;
}
