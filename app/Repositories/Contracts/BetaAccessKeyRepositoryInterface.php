<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BetaAccessKey;
use Illuminate\Database\Eloquent\Collection;

interface BetaAccessKeyRepositoryInterface
{
    /** @return Collection<int, BetaAccessKey> */
    public function all(): Collection;

    public function findById(string $id): ?BetaAccessKey;

    public function findByKey(string $key): ?BetaAccessKey;

    public function existsByKey(string $key): bool;

    public function save(BetaAccessKey $betaAccessKey): BetaAccessKey;

    public function delete(BetaAccessKey $betaAccessKey): void;
}
