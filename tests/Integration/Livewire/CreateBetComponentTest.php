<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Page\Bets\Create;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class CreateBetComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_with_default_options(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertSet('optionCount', 2)
            ->assertCount('options', 2);
    }

    public function test_adds_and_removes_options(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->call('addOption')
            ->assertSet('optionCount', 3)
            ->assertCount('options', 3)
            ->call('removeOption', 0)
            ->assertSet('optionCount', 2)
            ->assertCount('options', 2);
    }

    public function test_does_not_remove_below_minimum_options(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->call('removeOption', 0)
            ->assertSet('optionCount', 2)
            ->assertCount('options', 2);
    }

    public function test_recalculates_odds_when_adding_option(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->call('addOption')
            ->assertSet('options.0.odds', 2.0)
            ->assertSet('options.1.odds', 2.0)
            ->assertSet('options.2.odds', 2.0);
    }

    public function test_creates_bet_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Test Bet Title')
            ->set('description', 'A test description')
            ->set('options.0.title', 'Option A')
            ->set('options.0.odds', 2.0)
            ->set('options.1.title', 'Option B')
            ->set('options.1.odds', 1.5)
            ->call('createBet')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('bets', ['title' => 'Test Bet Title']);
        $this->assertDatabaseHas('bet_options', ['title' => 'Option A']);
        $this->assertDatabaseHas('bet_options', ['title' => 'Option B']);
    }

    public function test_validation_fails_for_empty_option_titles(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'A valid title')
            ->call('createBet')
            ->assertHasErrors(['options.0.title', 'options.1.title']);
    }

    public function test_validation_fails_for_odds_below_minimum(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('title', 'Test Bet')
            ->set('options.0.title', 'Option A')
            ->set('options.0.odds', 1.0)
            ->set('options.1.title', 'Option B')
            ->set('options.1.odds', 1.5)
            ->call('createBet')
            ->assertHasErrors(['options.0.odds']);
    }
}
