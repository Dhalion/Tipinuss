<?php

declare(strict_types=1);

namespace Tests\Browser\UserJourneys;

use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

final class UserRegistersWithBetaKeyTest extends BrowserTestCase
{
    public function test_user_registers_with_valid_beta_key_and_is_approved(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/register')
                ->waitForText('Registrieren')
                ->assertSee('Mit Einladungscode');

            $browser->script("Livewire.first().\$set('hasBetaKey', true)");
            $browser->waitForText('Direkt loslegen')
                ->type('betaKey', 'ABCD-1234')
                ->type('name', 'NewUser')
                ->type('email', 'newuser@test.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('Direkt loslegen')
                ->waitForLocation('/')
                ->assertSee('Wetten')
                ->assertSee('Wette erstellen');
        });
    }
}
