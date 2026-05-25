<?php

declare(strict_types=1);

namespace Tests\Integration\Actions\Admin;

use App\Actions\Admin\AdjustUserBalanceAction;
use App\Enums\TransactionType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class AdjustUserBalanceActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_credits_balance(): void
    {
        $user = User::factory()->withBalance(500)->create();
        $action = app(AdjustUserBalanceAction::class);

        $action->execute($user, 200);

        $this->assertSame(700, $user->fresh()->soapnuts);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::AdminAdjustment->value,
            'amount' => 200,
        ]);
    }

    public function test_debits_balance(): void
    {
        $user = User::factory()->withBalance(500)->create();
        $action = app(AdjustUserBalanceAction::class);

        $action->execute($user, -200);

        $this->assertSame(300, $user->fresh()->soapnuts);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::AdminAdjustment->value,
            'amount' => -200,
        ]);
    }

    public function test_floors_balance_at_zero(): void
    {
        $user = User::factory()->withBalance(100)->create();
        $action = app(AdjustUserBalanceAction::class);

        $action->execute($user, -500);

        $this->assertSame(0, $user->fresh()->soapnuts);
    }
}
