<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Bets\PlacedBetsFeed;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class PlacedBetsFeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_with_placed_bets(): void
    {
        $user = User::factory()->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        UserBet::factory()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
        ]);

        Livewire::actingAs($user)
            ->test(PlacedBetsFeed::class, ['betId' => $bet->id])
            ->assertOk();
    }

    public function test_dispatch_count_changed_on_refresh(): void
    {
        $user = User::factory()->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        UserBet::factory()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
        ]);

        $component = Livewire::actingAs($user)
            ->test(PlacedBetsFeed::class, ['betId' => $bet->id]);

        UserBet::factory()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
        ]);

        $component
            ->call('refreshFeed')
            ->assertDispatched('bets-count-changed');
    }
}
