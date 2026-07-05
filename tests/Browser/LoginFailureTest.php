<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class LoginFailureTest extends DuskTestCase
{
    public function test_user_sees_error_with_invalid_credentials(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('login'))
                ->type('input[name="email"]', 'nonexistent@example.com')
                ->type('input[name="password"]', 'wrong-password')
                ->click('#login-submit')
                ->waitForText('Die eingegebenen Anmeldedaten sind ungültig.')
                ->assertPathIs('/login');
        });
    }
}
