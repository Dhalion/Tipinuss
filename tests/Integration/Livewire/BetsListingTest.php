<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Page\Bets\BetsListing;
use App\Models\Bet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class BetsListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_bet_list(): void
    {
        $user = User::factory()->create();
        Bet::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(BetsListing::class)
            ->assertOk();
    }

    public function test_refresh_resets_pagination(): void
    {
        $user = User::factory()->create();
        Bet::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(BetsListing::class)
            ->call('refresh')
            ->assertOk();
    }
}
