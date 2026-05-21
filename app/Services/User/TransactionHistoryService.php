<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTOs\Account\TransactionHistoryEntry;
use App\Enums\TransactionType;
use App\Models\BalanceTransaction;
use App\Models\User;
use App\Models\UserBet;
use App\Repositories\Contracts\BalanceTransactionRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;

final class TransactionHistoryService
{
    public function __construct(
        private BalanceTransactionRepositoryInterface $transactions,
        private UserBetRepositoryInterface $userBets,
    ) {}

    /** @return array<int, TransactionHistoryEntry> */
    public function forUser(User $user, int $limit = 20): array
    {
        $transactions = $this->transactions->recentForUser($user, $limit * 2);

        if ($transactions->isEmpty()) {
            return [];
        }

        /** @var array<int, string> $userBetIds */
        $userBetIds = $transactions
            ->pluck('user_bet_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        /** @var array<string, UserBet> $userBetMap */
        $userBetMap = [];
        if ($userBetIds !== []) {
            $userBetMap = $this->userBets
                ->findByIdsWithOptionAndBet($userBetIds)
                ->keyBy('id')
                ->all();
        }

        /** @var SupportCollection<string, Collection<int, BalanceTransaction>> $grouped */
        $grouped = $transactions->groupBy(fn (BalanceTransaction $transaction): string => $transaction->user_bet_id ?? $transaction->id);

        $entries = [];
        foreach ($grouped as $key => $group) {
            /** @var string $key */
            $userBet = $userBetMap[$key] ?? null;
            /** @var UserBet|null $userBet */
            $entries[] = $this->buildEntry($group, $userBet);
        }

        return $entries;
    }

    /** @param Collection<int, BalanceTransaction> $group */
    private function buildEntry(Collection $group, ?UserBet $userBet): TransactionHistoryEntry
    {
        /** @var BalanceTransaction|null $placedTransaction */
        $placedTransaction = $group->firstWhere('type', TransactionType::BetPlaced);

        /** @var BalanceTransaction|null $wonTransaction */
        $wonTransaction = $group->firstWhere('type', TransactionType::BetWon);

        $firstTransaction = $group->first();

        if ($firstTransaction === null) {
            return new TransactionHistoryEntry(
                id: (string) Str::uuid(),
                type: 'adjustment',
                amount: 0,
                balanceAfter: 0,
                description: __('account.adjustment'),
                badgeLabel: __('account.adjustment_badge'),
                badgeColor: 'blue',
            );
        }

        if ($placedTransaction !== null) {
            $betDescription = $placedTransaction->description ?? __('account.bet_unknown');

            if ($wonTransaction !== null) {
                $placedAmount = $placedTransaction->amount;
                $wonAmount = $wonTransaction->amount;
                $netAmount = $wonAmount - abs($placedAmount);

                return new TransactionHistoryEntry(
                    id: (string) Str::uuid(),
                    type: 'bet',
                    amount: $netAmount,
                    balanceAfter: $wonTransaction->balance_after,
                    description: $betDescription,
                    badgeLabel: __('bets.won'),
                    badgeColor: 'green',
                    createdAt: $placedTransaction->created_at?->toIso8601String(),
                );
            }

            if ($userBet !== null && $userBet->status->value === 'lost') {
                return new TransactionHistoryEntry(
                    id: (string) Str::uuid(),
                    type: 'bet',
                    amount: $placedTransaction->amount,
                    balanceAfter: $placedTransaction->balance_after,
                    description: $betDescription,
                    badgeLabel: __('bets.lost'),
                    badgeColor: 'red',
                    createdAt: $placedTransaction->created_at?->toIso8601String(),
                );
            }

            return new TransactionHistoryEntry(
                id: (string) Str::uuid(),
                type: 'bet',
                amount: $placedTransaction->amount,
                balanceAfter: $placedTransaction->balance_after,
                description: $betDescription,
                badgeLabel: __('bets.pending'),
                badgeColor: 'amber',
                createdAt: $placedTransaction->created_at?->toIso8601String(),
            );
        }

        return new TransactionHistoryEntry(
            id: (string) Str::uuid(),
            type: 'adjustment',
            amount: $firstTransaction->amount,
            balanceAfter: $firstTransaction->balance_after,
            description: $firstTransaction->description ?? __('account.adjustment'),
            badgeLabel: __('account.adjustment_badge'),
            badgeColor: 'blue',
            createdAt: $firstTransaction->created_at?->toIso8601String(),
        );
    }
}
