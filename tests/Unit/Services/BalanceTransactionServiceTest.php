<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\TransactionType;
use App\Models\User;
use App\Models\UserBet;
use App\Services\User\BalanceTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class BalanceTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_creates_transaction(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $service = app(BalanceTransactionService::class);

        $transaction = $service->log(
            user: $user,
            type: TransactionType::BetPlaced,
            amount: -200,
            balanceAfter: 800,
        );

        $this->assertDatabaseHas('balance_transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'type' => TransactionType::BetPlaced->value,
            'amount' => -200,
            'balance_after' => 800,
        ]);
    }

    public function test_log_stores_correct_balance_after(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $service = app(BalanceTransactionService::class);

        $transaction = $service->log(
            user: $user,
            type: TransactionType::AdminAdjustment,
            amount: 500,
            balanceAfter: 1500,
        );

        $this->assertSame(1500, $transaction->balance_after);
    }

    public function test_log_with_user_bet_id(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $userBet = \App\Models\UserBet::factory()->create(['user_id' => $user->id]);
        $service = app(BalanceTransactionService::class);

        $transaction = $service->log(
            user: $user,
            type: TransactionType::BetPlaced,
            amount: -200,
            balanceAfter: 800,
            userBetId: $userBet->id,
        );

        $this->assertSame($userBet->id, $transaction->user_bet_id);
    }

    public function test_log_with_description(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $service = app(BalanceTransactionService::class);

        $transaction = $service->log(
            user: $user,
            type: TransactionType::AdminAdjustment,
            amount: 100,
            balanceAfter: 1100,
            description: 'Test adjustment',
        );

        $this->assertSame('Test adjustment', $transaction->description);
    }
}
