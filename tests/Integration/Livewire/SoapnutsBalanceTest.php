<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\SoapnutsBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class SoapnutsBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_user_balance(): void
    {
        $user = User::factory()->withBalance(500)->create();

        Livewire::actingAs($user)
            ->test(SoapnutsBalance::class)
            ->assertOk();
    }

    public function test_does_not_render_for_guest(): void
    {
        Livewire::test(SoapnutsBalance::class)
            ->assertOk();
    }

    public function test_refresh_dispatches_on_bet_placed_event(): void
    {
        $user = User::factory()->withBalance(500)->create();

        $component = Livewire::actingAs($user)->test(SoapnutsBalance::class);

        $component->dispatch('bet-placed');

        $component->assertOk();
    }
}
