<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Constants\AppDefaults;
use App\Models\BetaAccessKey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BetaAccessKeyRepositoryInterface
{
    /** @return Collection<int, BetaAccessKey> */
    public function all(): Collection;

    public function findById(string $id): ?BetaAccessKey;

    public function findByKey(string $key): ?BetaAccessKey;

    public function existsByKey(string $key): bool;

    public function paginate(
        string $sortBy = 'key',
        string $sortDirection = 'asc',
        int $perPage = AppDefaults::DEFAULT_PER_PAGE,
    ): LengthAwarePaginator;

    public function save(BetaAccessKey $betaAccessKey): BetaAccessKey;

    public function delete(BetaAccessKey $betaAccessKey): void;
}
