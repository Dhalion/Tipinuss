<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Bet;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class CreateAndPlaceBetTest extends DuskTestCase
{
    public function test_create_bet_and_place_bet_updates_balance(): void
    {
        $user = User::factory()->withBalance(5000)->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->formLoginAs($user)
                ->visitRoute('bets.create')
                ->waitFor('#bet-create-page')
                ->assertSee('Neue Wette erstellen')
                ->type('#bet-create-title', 'Test Bet Title')
                ->type('#bet-create-description', 'Test Description')
                ->type('#bet-create-option-0-title', 'Option A')
                ->type('#bet-create-option-1-title', 'Option B')
                ->click('#bet-create-submit')
                ->waitForText('Test Bet Title')
                ->assertSee('Test Bet Title')
                ->assertSee('Option A')
                ->assertSee('Option B');

            $bet = Bet::where('title', 'Test Bet Title')->first();
            $optionA = $bet ? $bet->betOptions()->where('title', 'Option A')->first() : null;

            $expectedAfterBet = $user->fresh()->soapnuts - 100;

            $browser->click('#bet-option-'.$optionA->id)
                ->waitFor('#bet-place-modal')
                ->type('#bet-stake-input', '100')
                ->click('#bet-place-submit')
                ->waitForText('Wette platziert!')
                ->assertSee(number_format($expectedAfterBet, 0, '.', ','));
        });
    }
}
