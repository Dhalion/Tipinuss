<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class AdminOrganisationManagementTest extends DuskTestCase
{
    public function test_admin_can_create_organisation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->formLoginAs($admin)
                ->visit(route('admin.organisations'))
                ->waitForLocation('/admin/organisations')
                ->waitFor('#admin-organisations-page')
                ->assertSee('Organisationen verwalten')
                ->assertSee('Noch keine Organisationen vorhanden.')
                ->click('#organisation-create-toggle')
                ->waitForText('Neue Organisation')
                ->type('#organisation-create-name', 'Test Org')
                ->click('#organisation-create-submit')
                ->waitForText('Test Org')
                ->assertSee('Test Org');
        });
    }
}
