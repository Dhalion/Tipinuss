<?php

declare(strict_types=1);

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

final class GuestVisitsLandingPageTest extends BrowserTestCase
{
    public function test_guest_sees_landing_or_login_in_beta_mode(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/')
                ->waitForText('Anmelden')
                ->assertSee('Registrieren')
                ->assertSee('E-Mail')
                ->assertSee('Passwort');
        });
    }
}
