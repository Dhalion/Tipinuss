<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Constants\AppDefaults;
use App\Models\BalanceTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface BalanceTransactionRepositoryInterface
{
    public function save(BalanceTransaction $transaction): BalanceTransaction;

    /** @param array<string, mixed> $data */
    public function create(array $data): BalanceTransaction;

    /** @return Collection<int, BalanceTransaction> */
    public function recentForUser(User $user, int $limit = AppDefaults::RECENT_TRANSACTIONS_LIMIT): Collection;

    /** @return Collection<int, BalanceTransaction> */
    public function chartDataForUser(User $user, int $limit = AppDefaults::CHART_DATA_LIMIT): Collection;
}
