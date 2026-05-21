<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BalanceTransaction;
use App\Models\User;
use App\Repositories\Contracts\BalanceTransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentBalanceTransactionRepository implements BalanceTransactionRepositoryInterface
{
    public function save(BalanceTransaction $transaction): BalanceTransaction
    {
        $transaction->save();

        return $transaction;
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): BalanceTransaction
    {
        return $this->save(new BalanceTransaction($data));
    }

    public function recentForUser(User $user, int $limit = 20): Collection
    {
        return BalanceTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }

    public function chartDataForUser(User $user, int $limit = 100): Collection
    {
        return BalanceTransaction::where('user_id', $user->id)
            ->orderBy('created_at')
            ->take($limit)
            ->get(['balance_after', 'created_at']);
    }
}
