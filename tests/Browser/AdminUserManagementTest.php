<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Constants\AppDefaults;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class AdminUserManagementTest extends DuskTestCase
{
    public function test_admin_can_approve_user_and_adjust_balance(): void
    {
        $admin = User::factory()->admin()->create();
        $unapprovedUser = User::factory()->unapproved()->create([
            'name' => 'Max Mustermann',
            'email' => 'max@example.com',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $unapprovedUser) {
            $browser->formLoginAs($admin)
                ->visit(route('admin.users'))
                ->waitFor('#admin-users-page')
                ->assertSee('Nutzerverwaltung')
                ->assertSee($unapprovedUser->name)
                ->with("tr[wire\\:key='user-{$unapprovedUser->id}']", function (Browser $row) use ($unapprovedUser) {
                    $row->click('#user-approve-'.$unapprovedUser->id);
                })
                ->waitForText('Aktiv')
                ->assertSee('Aktiv');

            $browser->with("tr[wire\\:key='user-{$unapprovedUser->id}']", function (Browser $row) use ($unapprovedUser) {
                $row->click('#user-balance-adjust-'.$unapprovedUser->id);
            })
                ->waitForText('Guthaben anpassen')
                ->type('#balance-adjust-input', '500')
                ->click('#balance-adjust-submit')
                ->waitForText('Guthaben von Max Mustermann angepasst');
        });

        $this->assertSame(AppDefaults::START_BALANCE + 500, $unapprovedUser->fresh()->soapnuts);
    }
}
