<?php

declare(strict_types=1);

namespace Tests\Integration\Actions;

use App\Actions\Betting\CloseBetAction;
use App\DTOs\Betting\CloseBetData;
use App\Enums\BetStatus;
use App\Enums\TransactionType;
use App\Enums\UserBetStatus;
use App\Events\Betting\BetClosed;
use App\Exceptions\BetException;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Event;

final class CloseBetActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_closes_bet_successfully_with_winners_and_losers(): void
    {
        Event::fake();

        $admin = User::factory()->admin()->create();
        $winner1 = User::factory()->withBalance(500)->create();
        $winner2 = User::factory()->withBalance(300)->create();
        $loser = User::factory()->withBalance(200)->create();

        $bet = Bet::factory()->create(['user_id' => $admin->id]);
        $winningOption = BetOption::factory()->create(['bet_id' => $bet->id, 'odds' => 2.0]);
        $losingOption = BetOption::factory()->create(['bet_id' => $bet->id, 'odds' => 1.5]);

        UserBet::factory()->create([
            'user_id' => $winner1->id,
            'bet_option_id' => $winningOption->id,
            'amount_wagered' => 100,
            'potential_winnings' => 200,
            'status' => UserBetStatus::Pending,
        ]);

        UserBet::factory()->create([
            'user_id' => $winner2->id,
            'bet_option_id' => $winningOption->id,
            'amount_wagered' => 50,
            'potential_winnings' => 100,
            'status' => UserBetStatus::Pending,
        ]);

        UserBet::factory()->create([
            'user_id' => $loser->id,
            'bet_option_id' => $losingOption->id,
            'amount_wagered' => 75,
            'potential_winnings' => 112,
            'status' => UserBetStatus::Pending,
        ]);

        $bet->load('betOptions.userBets');

        $action = app(CloseBetAction::class);
        $this->actingAs($admin);

        $closedBet = $action->execute(new CloseBetData(bet: $bet, winningOptionId: $winningOption->id));

        $this->assertSame(BetStatus::Closed, $closedBet->status);

        $winningOption->refresh();
        $losingOption->refresh();

        $this->assertTrue($winningOption->result);
        $this->assertFalse($losingOption->result);

        $winner1->refresh();
        $winner2->refresh();
        $loser->refresh();

        $this->assertSame(700, $winner1->soapnuts);
        $this->assertSame(400, $winner2->soapnuts);
        $this->assertSame(200, $loser->soapnuts);

        $winner1->userBets()->first()->refresh();
        $loser->userBets()->first()->refresh();

        $this->assertSame(UserBetStatus::Won, $winner1->userBets()->first()->status);
        $this->assertSame(UserBetStatus::Won, $winner2->userBets()->first()->status);
        $this->assertSame(UserBetStatus::Lost, $loser->userBets()->first()->status);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $winner1->id,
            'type' => TransactionType::BetWon->value,
            'amount' => 200,
        ]);

        Event::assertDispatched(BetClosed::class);
    }

    public function test_throws_when_winning_option_does_not_belong_to_bet(): void
    {
        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->create(['user_id' => $admin->id]);
        $otherOption = BetOption::factory()->create();

        $action = app(CloseBetAction::class);

        $this->actingAs($admin);
        $this->expectException(BetException::class);
        $this->expectExceptionMessage(__('bets.wrong_bet_option'));

        $action->execute(new CloseBetData(bet: $bet, winningOptionId: $otherOption->id));
    }

    public function test_throws_when_user_not_authorized(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);

        $action = app(CloseBetAction::class);

        $this->expectExceptionMessage('This action is unauthorized.');

        $action->execute(new CloseBetData(bet: $bet, winningOptionId: $option->id));
    }

    public function test_closes_bet_with_no_participants(): void
    {
        Event::fake();

        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->create(['user_id' => $admin->id]);
        $winningOption = BetOption::factory()->create(['bet_id' => $bet->id]);
        BetOption::factory()->create(['bet_id' => $bet->id]);

        $bet->load('betOptions.userBets');

        $action = app(CloseBetAction::class);
        $this->actingAs($admin);

        $closedBet = $action->execute(new CloseBetData(bet: $bet, winningOptionId: $winningOption->id));

        $this->assertSame(BetStatus::Closed, $closedBet->status);
        $this->assertTrue($winningOption->fresh()->result);
    }
}
