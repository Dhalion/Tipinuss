<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function findById(string $id): ?User;

    /** @return Collection<int, User> */
    public function all(): Collection;

    /** @return Collection<int, User> */
    public function allWithBetCount(): Collection;

    /** @return Collection<int, User> */
    public function allWithBetCountByApprovalStatus(?bool $isApproved): Collection;

    public function pendingCount(): int;

    public function save(User $user): User;

    /** @return Collection<int, User> */
    public function topBySoapnuts(int $limit = 10): Collection;

    public function delete(User $user): void;
}
