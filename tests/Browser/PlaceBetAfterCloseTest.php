<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class PlaceBetAfterCloseTest extends DuskTestCase
{
    public function test_cannot_place_bet_on_closed_bet(): void
    {
        $user = User::factory()->withBalance(1000)->create();

        $bet = Bet::factory()->closed()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id, 'title' => 'Final Option']);

        $this->browse(function (Browser $browser) use ($user, $bet, $option) {
            $browser->formLoginAs($user)
                ->visitRoute('bets.detail', ['bet' => $bet->slugUrl()])
                ->waitFor('#bet-detail-page')
                ->assertSee($bet->title)
                ->assertSee('Geschlossen')
                ->assertSee('Final Option')
                ->assertMissing('#bet-option-'.$option->id);
        });
    }
}
