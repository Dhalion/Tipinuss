<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Enums\UserBetStatus;
use App\Livewire\Page\Bets\BetDetail;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class BetDetailComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_bet_successfully(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id, 'odds' => 2.0]);

        Livewire::actingAs($user)
            ->test(BetDetail::class, ['bet' => $bet])
            ->call('placeBet', $option->id, 100)
            ->assertHasNoErrors('amount')
            ->assertDispatched('bet-placed')
            ->assertDispatched('refresh-placed-bets');

        $this->assertDatabaseHas('user_bets', [
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
            'amount_wagered' => 100,
            'potential_winnings' => 200,
            'status' => UserBetStatus::Pending->value,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'soapnuts' => 900,
        ]);
    }

    public function test_place_bet_fails_with_invalid_amount(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);

        Livewire::actingAs($user)
            ->test(BetDetail::class, ['bet' => $bet])
            ->call('placeBet', $option->id, -10)
            ->assertHasErrors('amount');
    }

    public function test_place_bet_fails_when_bet_closed(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->closed()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);

        Livewire::actingAs($user)
            ->test(BetDetail::class, ['bet' => $bet])
            ->call('placeBet', $option->id, 100)
            ->assertHasErrors('amount');
    }

    public function test_place_bet_fails_with_insufficient_funds(): void
    {
        $user = User::factory()->withBalance(10)->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);

        Livewire::actingAs($user)
            ->test(BetDetail::class, ['bet' => $bet])
            ->call('placeBet', $option->id, 100)
            ->assertHasErrors('amount');
    }
}
