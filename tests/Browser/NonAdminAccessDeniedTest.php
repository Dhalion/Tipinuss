<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class NonAdminAccessDeniedTest extends DuskTestCase
{
    public function test_non_admin_user_gets_forbidden_on_admin_route(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->formLoginAs($user)
                ->visit(route('admin.users'))
                ->waitForText('403')
                ->assertSee('403');
        });
    }
}
