<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class CloseBetTest extends DuskTestCase
{
    public function test_bet_creator_can_close_bet_and_winners_get_paid(): void
    {
        $creator = User::factory()->create();
        $bettor1 = User::factory()->withBalance(1000)->create();
        $bettor2 = User::factory()->withBalance(1000)->create();

        $bet = Bet::factory()->create(['user_id' => $creator->id]);
        $optionA = BetOption::factory()->create(['bet_id' => $bet->id, 'title' => 'Team A', 'odds' => 2.0]);
        $optionB = BetOption::factory()->create(['bet_id' => $bet->id, 'title' => 'Team B', 'odds' => 3.0]);

        UserBet::factory()->create([
            'user_id' => $bettor1->id,
            'bet_option_id' => $optionA->id,
            'amount_wagered' => 100,
            'potential_winnings' => 200,
        ]);

        UserBet::factory()->create([
            'user_id' => $bettor2->id,
            'bet_option_id' => $optionB->id,
            'amount_wagered' => 200,
            'potential_winnings' => 600,
        ]);

        $this->browse(function (Browser $browser) use ($creator, $bet, $optionB) {
            $browser->formLoginAs($creator)
                ->visitRoute('bets.detail', ['bet' => $bet->slugUrl()])
                ->waitFor('#bet-detail-page')
                ->assertSee($bet->title)
                ->click('#bet-close-'.$bet->id)
                ->waitFor('#bet-close-modal')
                ->assertSee('Wähle die gewinnerlose Option aus')
                ->click('#bet-close-option-'.$optionB->id)
                ->waitFor('[data-flux-toast-dialog][data-variant="success"]')
                ->assertSee('geschlossen und Gewinne ausgezahlt');
        });

        $this->assertDatabaseHas('bets', [
            'id' => $bet->id,
            'status' => 'closed',
        ]);

        $this->assertDatabaseHas('bet_options', [
            'id' => $optionB->id,
            'result' => 1,
        ]);

        $this->assertSame(1600, $bettor2->fresh()->soapnuts);
        $this->assertSame(1000, $bettor1->fresh()->soapnuts);
    }
}
