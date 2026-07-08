<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\BetaAccessKey;
use App\Models\Organisation;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class AdminBetaKeysTest extends DuskTestCase
{
    public function test_admin_can_create_and_deactivate_beta_key(): void
    {
        $admin = User::factory()->admin()->create();
        $organisation = Organisation::factory()->create(['name' => 'Test Organisation']);

        $this->browse(function (Browser $browser) use ($admin, $organisation) {
            $browser->formLoginAs($admin)
                ->visit(route('admin.beta-keys'))
                ->waitFor('#admin-beta-keys-page')
                ->assertSee('Beta-Zugangsschlüssel')
                ->assertSee('Keine Schlüssel vorhanden')
                ->click('#beta-key-create-toggle')
                ->waitForText('Neuen Zugangsschlüssel erstellen')
                ->select('#beta-key-organisation', $organisation->id)
                ->click('#beta-key-create-submit')
                ->waitForText($organisation->name)
                ->assertSee('Verfügbar');

            $key = BetaAccessKey::latest('id')->first();

            $browser->click('#beta-key-deactivate-'.$key->id)
                ->acceptDialog()
                ->waitForText('Inaktiv')
                ->assertSee('Inaktiv');
        });
    }
}
