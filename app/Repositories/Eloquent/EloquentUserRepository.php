<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Constants\AppDefaults;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final class EloquentUserRepository implements UserRepositoryInterface
{
    private const array SORTABLE_COLUMNS = [
        'name', 'soapnuts', 'is_approved', 'is_admin',
        'created_at', 'user_bets_count',
    ];

    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function all(): Collection
    {
        return User::orderBy('name')->get();
    }

    public function allWithBetCount(): Collection
    {
        return User::withCount('userBets')
            ->with('organisation')
            ->orderBy('name')
            ->get();
    }

    public function paginateWithBetCountByApprovalStatus(
        ?bool $isApproved,
        string $sortBy = 'name',
        string $sortDirection = 'asc',
        int $perPage = AppDefaults::DEFAULT_PER_PAGE,
    ): LengthAwarePaginator {
        $sortBy = in_array($sortBy, self::SORTABLE_COLUMNS, true) ? $sortBy : 'name';
        $sortDirection = $sortDirection === 'desc' ? 'desc' : 'asc';

        $query = User::withCount('userBets')
            ->with('organisation');

        if ($isApproved !== null) {
            $query->where('is_approved', $isApproved);
        }

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function pendingCount(): int
    {
        return User::where('is_approved', false)->count();
    }

    public function save(User $user): User
    {
        $user->save();

        return $user;
    }

    public function topBySoapnuts(int $limit = AppDefaults::TOP_USERS_LIMIT, ?string $organisationId = null): Collection
    {
        $query = User::withCount('userBets');

        if ($organisationId !== null) {
            $query->where(function ($q) use ($organisationId): void {
                $q->where('organisation_id', $organisationId)
                    ->orWhereNull('organisation_id');
            });
        }

        return $query->orderByDesc('soapnuts')
            ->take($limit)
            ->get();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
