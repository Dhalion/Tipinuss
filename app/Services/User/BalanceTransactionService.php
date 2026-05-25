<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Enums\TransactionType;
use App\Models\BalanceTransaction;
use App\Models\User;
use App\Repositories\Contracts\BalanceTransactionRepositoryInterface;

final class BalanceTransactionService
{
    public function __construct(
        private BalanceTransactionRepositoryInterface $transactions,
    ) {}

    public function log(
        User $user,
        TransactionType $type,
        int $amount,
        int $balanceAfter,
        ?string $userBetId = null,
        ?string $description = null,
    ): BalanceTransaction {
        return $this->transactions->create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'user_bet_id' => $userBetId,
            'description' => $description,
        ]);
    }
}
