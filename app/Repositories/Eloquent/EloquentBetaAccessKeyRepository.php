<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BetaAccessKey;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentBetaAccessKeyRepository implements BetaAccessKeyRepositoryInterface
{
    public function all(): Collection
    {
        return BetaAccessKey::with('organisation', 'usedByUser', 'createdByUser')
            ->orderByDesc('created_at')
            ->get();
    }

    public function findById(string $id): ?BetaAccessKey
    {
        return BetaAccessKey::find($id);
    }

    public function findByKey(string $key): ?BetaAccessKey
    {
        return BetaAccessKey::where('key', $key)->first();
    }

    public function existsByKey(string $key): bool
    {
        return BetaAccessKey::where('key', $key)->exists();
    }

    public function save(BetaAccessKey $betaAccessKey): BetaAccessKey
    {
        $betaAccessKey->save();

        return $betaAccessKey;
    }

    public function delete(BetaAccessKey $betaAccessKey): void
    {
        $betaAccessKey->delete();
    }
}
