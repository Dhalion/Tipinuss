<?php

declare(strict_types=1);

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class RegistrationOpenModeTest extends DuskTestCase
{
    public function test_unapproved_registration_without_beta_key_redirects_to_pending(): void
    {
        $email = 'open-user@example.com';

        $this->browse(function (Browser $browser) use ($email) {
            $browser->visit(route('register'))
                ->waitFor('#register-form')
                ->click('#reg-path-no-key')
                ->type('[wire\\:model="name"]', 'Open User')
                ->type('[wire\\:model="email"]', $email)
                ->type('[wire\\:model="password"]', 'password')
                ->type('[wire\\:model="password_confirmation"]', 'password')
                ->click('#register-submit')
                ->waitForLocation('/pending')
                ->assertSee('Account wartet auf Freischaltung')
                ->assertPresent('#user-menu');
        });

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'is_approved' => 0,
        ]);
    }
}
