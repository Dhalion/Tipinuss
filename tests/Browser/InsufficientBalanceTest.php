<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class InsufficientBalanceTest extends DuskTestCase
{
    public function test_user_sees_error_when_placing_bet_above_balance(): void
    {
        $user = User::factory()->withBalance(10)->create();

        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id, 'title' => 'Option X']);

        $this->browse(function (Browser $browser) use ($user, $bet, $option) {
            $browser->formLoginAs($user)
                ->visitRoute('bets.detail', ['bet' => $bet->slugUrl()])
                ->waitFor('#bet-detail-page')
                ->assertSee($bet->title)
                ->click('#bet-option-'.$option->id)
                ->waitFor('#bet-place-modal')
                ->assertSee('Einsatz in')
                ->type('#bet-stake-input', '100')
                ->click('#bet-place-submit')
                ->waitForText('Nicht genügend Guthaben')
                ->assertSee('Nicht genügend Guthaben');
        });
    }
}
