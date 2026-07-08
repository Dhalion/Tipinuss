<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class PendingApprovalFlowTest extends DuskTestCase
{
    public function test_unapproved_user_is_redirected_to_pending_after_login(): void
    {
        $user = User::factory()->unapproved()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->formLoginAs($user)
                ->assertPathIs('/pending')
                ->assertSee('Account wartet auf Freischaltung')
                ->assertPresent('#user-menu');
        });
    }

    public function test_approved_user_can_access_home_after_login(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->formLoginAs($user)
                ->assertPathIs('/')
                ->waitFor('#user-menu', 15)
                ->assertSee('Online Waschnusswetten');
        });
    }
}
