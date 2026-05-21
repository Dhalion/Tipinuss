<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Enums\TransactionType;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\User\BalanceTransactionService;
use Illuminate\Support\Facades\DB;

final class AdjustUserBalanceAction
{
    public function __construct(
        private UserRepositoryInterface $users,
        private BalanceTransactionService $balanceTransactions,
    ) {}

    public function execute(User $target, int $adjustment): void
    {
        DB::transaction(function () use ($target, $adjustment): void {
            $newBalance = max(0, $target->soapnuts + $adjustment);
            $target->soapnuts = $newBalance;
            $this->users->save($target);

            $description = $adjustment >= 0
                ? __('account.adjustment_credit', ['amount' => number_format($adjustment)])
                : __('account.adjustment_debit', ['amount' => number_format(abs($adjustment))]);

            $this->balanceTransactions->log(
                user: $target,
                type: TransactionType::AdminAdjustment,
                amount: $adjustment,
                description: $description,
            );
        });
    }
}
