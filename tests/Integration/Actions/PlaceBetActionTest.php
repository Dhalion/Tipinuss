<?php

declare(strict_types=1);

namespace Tests\Integration\Actions;

use App\Actions\Betting\PlaceBetAction;
use App\DTOs\Betting\PlaceBetData;
use App\Enums\TransactionType;
use App\Enums\UserBetStatus;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class PlaceBetActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_executes_place_bet_successfully(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $option = BetOption::factory()->create(['odds' => 2.50]);
        $action = app(PlaceBetAction::class);

        $userBet = $action->execute(new PlaceBetData(user: $user, option: $option, amount: 200));

        $this->assertInstanceOf(UserBet::class, $userBet);
        $this->assertSame(200, $userBet->amount_wagered);
        $this->assertSame(500, $userBet->potential_winnings);
        $this->assertSame(UserBetStatus::Pending, $userBet->status);
        $this->assertSame($option->id, $userBet->bet_option_id);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'soapnuts' => 800,
        ]);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::BetPlaced->value,
            'amount' => -200,
            'user_bet_id' => $userBet->id,
        ]);
    }

    public function test_throws_when_bet_is_closed(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->closed()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        $action = app(PlaceBetAction::class);

        $this->expectExceptionMessage(__('bets.bet_closed_error'));

        $action->execute(new PlaceBetData(user: $user, option: $option, amount: 100));
    }

    public function test_throws_when_bet_is_expired(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->expired()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        $action = app(PlaceBetAction::class);

        $this->expectExceptionMessage(__('bets.bet_expired_error'));

        $action->execute(new PlaceBetData(user: $user, option: $option, amount: 100));
    }

    public function test_throws_when_amount_below_minimum(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $option = BetOption::factory()->create();
        $action = app(PlaceBetAction::class);

        $this->expectExceptionMessage(__('bets.amount_out_of_bounds', ['min' => 1, 'max' => 100000]));

        $action->execute(new PlaceBetData(user: $user, option: $option, amount: 0));
    }

    public function test_throws_when_amount_above_maximum(): void
    {
        $user = User::factory()->withBalance(200_000)->create();
        $option = BetOption::factory()->create();
        $action = app(PlaceBetAction::class);

        $this->expectExceptionMessage(__('bets.amount_out_of_bounds', ['min' => 1, 'max' => 100000]));

        $action->execute(new PlaceBetData(user: $user, option: $option, amount: 100_001));
    }

    public function test_throws_when_insufficient_balance(): void
    {
        $user = User::factory()->withBalance(50)->create();
        $option = BetOption::factory()->create();
        $action = app(PlaceBetAction::class);

        $this->expectExceptionMessage(__('bets.insufficient_balance', ['shortfall' => 50]));

        $action->execute(new PlaceBetData(user: $user, option: $option, amount: 100));
    }

    public function test_place_bet_is_atomic_on_failure(): void
    {
        $user = User::factory()->withBalance(100)->create();
        $option = BetOption::factory()->create(['odds' => 2.0]);
        $action = app(PlaceBetAction::class);

        try {
            $action->execute(new PlaceBetData(user: $user, option: $option, amount: 200));
        } catch (\Throwable) {
        }

        $this->assertDatabaseHas('users', ['id' => $user->id, 'soapnuts' => 100]);
        $this->assertDatabaseCount('balance_transactions', 0);
        $this->assertDatabaseCount('user_bets', 0);
    }
}
