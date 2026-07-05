<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\BetaAccessKey;
use App\Models\Organisation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class ExpiredBetaKeyTest extends DuskTestCase
{
    public function test_registration_fails_with_expired_beta_key(): void
    {
        $organisation = Organisation::factory()->create();
        $betaKey = BetaAccessKey::factory()->expired()->create([
            'organisation_id' => $organisation->id,
        ]);

        $this->browse(function (Browser $browser) use ($betaKey) {
            $browser->visit(route('register'))
                ->waitFor('#register-form')
                ->click('#reg-path-key')
                ->waitFor('#reg-beta-key')
                ->type('[wire\\:model="name"]', 'Expired Key User')
                ->type('[wire\\:model="email"]', 'expired-key@example.com')
                ->type('[wire\\:model="password"]', 'password')
                ->type('[wire\\:model="password_confirmation"]', 'password')
                ->type('#reg-beta-key', $betaKey->key)
                ->click('#register-submit')
                ->waitForText('Dieser Zugangscode ist abgelaufen.')
                ->assertPathIs('/register');
        });
    }
}
