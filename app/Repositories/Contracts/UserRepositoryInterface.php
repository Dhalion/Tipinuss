<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Constants\AppDefaults;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    public function findByEmail(string $email): ?User;

    /** @return Collection<int, User> */
    public function all(): Collection;

    /** @return Collection<int, User> */
    public function allWithBetCount(): Collection;

    public function paginateWithBetCountByApprovalStatus(
        ?bool $isApproved,
        string $sortBy = 'name',
        string $sortDirection = 'asc',
        int $perPage = AppDefaults::DEFAULT_PER_PAGE,
    ): LengthAwarePaginator;

    public function pendingCount(): int;

    public function save(User $user): User;

    /** @return Collection<int, User> */
    public function topBySoapnuts(int $limit = AppDefaults::TOP_USERS_LIMIT, ?string $organisationId = null): Collection;

    public function delete(User $user): void;
}
