<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BalanceTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface BalanceTransactionRepositoryInterface
{
    public function save(BalanceTransaction $transaction): BalanceTransaction;

    /** @param array<string, mixed> $data */
    public function create(array $data): BalanceTransaction;

    /** @return Collection<int, BalanceTransaction> */
    public function recentForUser(User $user, int $limit = 20): Collection;

    /** @return Collection<int, BalanceTransaction> */
    public function chartDataForUser(User $user, int $limit = 100): Collection;
}
