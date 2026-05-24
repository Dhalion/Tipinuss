<?php

declare(strict_types=1);

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

final class UserViewsAccountTest extends BrowserTestCase
{
    public function test_user_views_profile_balance_and_stats(): void
    {
        $this->browse(function (Browser $browser): void {
            $this->loginAs($browser, 'alice@test.com');

            $browser->visit('/account')
                ->waitForText('Profil')
                ->assertSee('Alice')
                ->assertSee('alice@test.com')
                ->assertSee('Soapnuts')
                ->assertSee('500')
                ->assertSee('Chart');
        });
    }
}
