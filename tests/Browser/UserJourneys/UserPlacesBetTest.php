<?php

declare(strict_types=1);

namespace Tests\Browser\UserJourneys;

use App\Models\Bet;
use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

final class UserPlacesBetTest extends BrowserTestCase
{
    public function test_user_logs_in_and_places_bet_on_open_option(): void
    {
        $bet = Bet::where('title', 'Wer gewinnt das Turnier?')->firstOrFail();

        $this->browse(function (Browser $browser) use ($bet): void {
            $this->loginAs($browser, 'alice@test.com');

            $browser->visit('/bets/'.$bet->slugUrl())
                ->screenshot('bet-detail-after-visit')
                ->waitForText('Team Alpha')
                ->assertSee('Team Beta')
                ->assertSee('2.50x')
                ->clickAtXPath("//button[contains(., 'Team Alpha')]")
                ->waitForText('Einsatz')
                ->script([
                    "document.querySelector('[x-ref=amountInput]').value = '50'",
                    "document.querySelector('[x-ref=amountInput]').dispatchEvent(new Event('input', {bubbles: true}))",
                ]);

            $browser->pause(300)
                ->press('Wette platzieren')
                ->waitForText('Wette platziert');
        });
    }
}
