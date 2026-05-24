<?php

declare(strict_types=1);

namespace Tests\Browser\UserJourneys;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\BrowserTestCase;

final class UserRegistersWithoutBetaKeyTest extends BrowserTestCase
{
    public function test_user_registers_without_key_sees_pending_then_admin_approves(): void
    {
        $this->browse(function (Browser $browser): void {
            $browser->visit('/register')
                ->waitForText('Registrieren')
                ->clickAtXPath("//label[contains(., 'Ohne Code')]")
                ->type('name', 'PendingUser')
                ->type('email', 'pending@test.com')
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press('Freischaltung abwarten')
                ->waitForText('Account wartet auf Freischaltung')
                ->assertSee('Prüfe auf Freischaltung');

            $browser->assertDontSee('Wetten')
                ->assertDontSee('Wette erstellen');

            User::where('email', 'pending@test.com')->update(['is_approved' => true]);

            $browser->waitForLocation('/', seconds: 15)
                ->assertSee('Wetten');
        });
    }
}
