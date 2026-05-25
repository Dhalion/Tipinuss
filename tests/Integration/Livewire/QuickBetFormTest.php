<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Bets\QuickBetForm;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class QuickBetFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_with_option(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);

        Livewire::actingAs($user)
            ->test(QuickBetForm::class, ['optionId' => $option->id])
            ->assertOk();
    }

    public function test_validates_amount_required(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);

        Livewire::actingAs($user)
            ->test(QuickBetForm::class, ['optionId' => $option->id])
            ->set('amount', 0)
            ->assertHasErrors('amount');
    }
}
