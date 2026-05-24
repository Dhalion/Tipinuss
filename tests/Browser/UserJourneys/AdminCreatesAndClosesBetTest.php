<?php

declare(strict_types=1);

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

final class AdminCreatesAndClosesBetTest extends BrowserTestCase
{
    public function test_admin_creates_bet_then_closes_with_winner(): void
    {
        $this->browse(function (Browser $browser): void {
            $this->loginAs($browser, 'admin@test.com');

            $browser->visit('/bet/create')
                ->waitForText('Wette erstellen')
                ->screenshot('admin-before-form');

            $browser->keys('[name="title"]', 'Testwette Dusk')
                ->keys('[name="description"]', 'Beschreibung')
                ->keys('[name="options.0.title"]', 'Option A')
                ->keys('[name="options.1.title"]', 'Option B')
                ->pause(300)
                ->press('Wette erstellen')
                ->waitForText('Testwette Dusk')
                ->assertSee('Option A')
                ->assertSee('Option B');

            $browser->press('Wette schließen')
                ->waitForText('Wähle die gewinnerlose Option aus');

            $browser->script("document.querySelector('[wire\\\\:key^=\"option-bets-\"]').click()");

            $browser->pause(500)
                ->waitForText('Geschlossen');
        });
    }
}
