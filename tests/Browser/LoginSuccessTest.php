<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class LoginSuccessTest extends DuskTestCase
{
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'login-success@example.com',
            'password' => 'password',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit(route('login'))
                ->waitForText('Anmelden')
                ->type('input[name="email"]', $user->email)
                ->type('input[name="password"]', 'password')
                ->click('#login-submit')
                ->waitForText('Online Waschnusswetten', 15)
                ->assertSee('Online Waschnusswetten');
        });
    }
}
