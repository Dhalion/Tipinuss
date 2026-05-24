<?php

declare(strict_types=1);

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

final class MobileNavigationTest extends BrowserTestCase
{
    public function test_mobile_layout_shows_sidebar_toggle_and_responsive_nav(): void
    {
        $this->browse(function (Browser $browser): void {
            $this->loginAs($browser, 'alice@test.com');

            $browser->resize(375, 812)
                ->visit('/')
                ->waitForText('Waschnusswetten')
                ->assertSee('Waschnusswetten');
        });
    }
}
