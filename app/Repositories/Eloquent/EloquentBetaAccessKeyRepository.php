<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Constants\AppDefaults;
use App\Models\BetaAccessKey;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class EloquentBetaAccessKeyRepository implements BetaAccessKeyRepositoryInterface
{
    private const array SORTABLE_COLUMNS = [
        'key', 'organisation_id', 'is_active', 'expires_at',
        'start_balance', 'created_at',
    ];

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

    public function paginate(
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = AppDefaults::DEFAULT_PER_PAGE,
    ): LengthAwarePaginator {
        $sortBy = in_array($sortBy, self::SORTABLE_COLUMNS, true) ? $sortBy : 'created_at';
        $sortDirection = $sortDirection === 'desc' ? 'desc' : 'asc';

        return BetaAccessKey::with('organisation', 'usedByUser', 'createdByUser')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);
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
