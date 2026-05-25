<?php

declare(strict_types=1);

namespace Tests\Integration\Services;

use App\Enums\TransactionType;
use App\Models\BalanceTransaction;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use App\Services\User\TransactionHistoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class TransactionHistoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_empty_array_when_no_transactions(): void
    {
        $user = User::factory()->create();
        $service = app(TransactionHistoryService::class);

        $entries = $service->forUser($user);

        $this->assertSame([], $entries);
    }

    public function test_shows_won_bet_correctly(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id, 'odds' => 2.0]);
        $userBet = UserBet::factory()->won()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
            'amount_wagered' => 100,
            'potential_winnings' => 200,
        ]);

        $placed = new BalanceTransaction([
            'user_id' => $user->id,
            'type' => TransactionType::BetPlaced,
            'amount' => -100,
            'balance_after' => 900,
            'user_bet_id' => $userBet->id,
            'description' => $bet->title.' — '.$option->title,
        ]);
        $placed->save();
        $placed->created_at = now()->subMinute();
        $placed->save();

        $won = new BalanceTransaction([
            'user_id' => $user->id,
            'type' => TransactionType::BetWon,
            'amount' => 200,
            'balance_after' => 1100,
            'user_bet_id' => $userBet->id,
        ]);
        $won->save();

        $service = app(TransactionHistoryService::class);
        $entries = $service->forUser($user);

        $this->assertCount(1, $entries);
        $this->assertSame('bet', $entries[0]->type);
        $this->assertSame(100, $entries[0]->amount);
        $this->assertSame(__('bets.won'), $entries[0]->badgeLabel);
        $this->assertNotNull($entries[0]->betRoute);
    }

    public function test_shows_lost_bet_correctly(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id, 'odds' => 2.0]);
        $userBet = UserBet::factory()->lost()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
            'amount_wagered' => 100,
            'potential_winnings' => 200,
        ]);

        $placed = new BalanceTransaction([
            'user_id' => $user->id,
            'type' => TransactionType::BetPlaced,
            'amount' => -100,
            'balance_after' => 900,
            'user_bet_id' => $userBet->id,
            'description' => $bet->title.' — '.$option->title,
        ]);
        $placed->save();

        $service = app(TransactionHistoryService::class);
        $entries = $service->forUser($user);

        $this->assertCount(1, $entries);
        $this->assertSame('bet', $entries[0]->type);
        $this->assertSame(-100, $entries[0]->amount);
        $this->assertSame(__('bets.lost'), $entries[0]->badgeLabel);
        $this->assertNotNull($entries[0]->betRoute);
    }

    public function test_shows_pending_bet_correctly(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        $userBet = UserBet::factory()->pending()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
            'amount_wagered' => 100,
            'potential_winnings' => 200,
        ]);

        $placed = new BalanceTransaction([
            'user_id' => $user->id,
            'type' => TransactionType::BetPlaced,
            'amount' => -100,
            'balance_after' => 900,
            'user_bet_id' => $userBet->id,
            'description' => $bet->title.' — '.$option->title,
        ]);
        $placed->save();

        $service = app(TransactionHistoryService::class);
        $entries = $service->forUser($user);

        $this->assertCount(1, $entries);
        $this->assertSame('bet', $entries[0]->type);
        $this->assertSame(-100, $entries[0]->amount);
        $this->assertSame(__('bets.pending'), $entries[0]->badgeLabel);
        $this->assertNotNull($entries[0]->betRoute);
    }

    public function test_shows_adjustment_correctly(): void
    {
        $user = User::factory()->withBalance(1000)->create();

        $adjustment = new BalanceTransaction([
            'user_id' => $user->id,
            'type' => TransactionType::AdminAdjustment,
            'amount' => 200,
            'balance_after' => 1200,
            'description' => 'Credit adjustment',
        ]);
        $adjustment->save();

        $service = app(TransactionHistoryService::class);
        $entries = $service->forUser($user);

        $this->assertCount(1, $entries);
        $this->assertSame('adjustment', $entries[0]->type);
        $this->assertSame(200, $entries[0]->amount);
        $this->assertSame(__('account.adjustment_badge'), $entries[0]->badgeLabel);
    }
}
