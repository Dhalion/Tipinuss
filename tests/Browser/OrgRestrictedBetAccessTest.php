<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\Organisation;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class OrgRestrictedBetAccessTest extends DuskTestCase
{
    public function test_user_from_different_organisation_gets_403(): void
    {
        $orgA = Organisation::factory()->create();
        $orgB = Organisation::factory()->create();

        $userA = User::factory()->create(['organisation_id' => $orgA->id]);
        $userB = User::factory()->create(['organisation_id' => $orgB->id]);

        $bet = Bet::factory()->create([
            'organisation_id' => $orgA->id,
            'user_id' => $userA->id,
        ]);

        BetOption::factory()->create(['bet_id' => $bet->id, 'title' => 'Secret Option']);

        $this->browse(function (Browser $browser) use ($userB, $bet) {
            $browser->formLoginAs($userB)
                ->visitRoute('bets.detail', ['bet' => $bet->slugUrl()])
                ->waitForText('403')
                ->assertSee('403');
        });
    }

    public function test_user_from_same_organisation_can_view_bet(): void
    {
        $org = Organisation::factory()->create();

        $creator = User::factory()->create(['organisation_id' => $org->id]);
        $viewer = User::factory()->create(['organisation_id' => $org->id]);

        $bet = Bet::factory()->create([
            'organisation_id' => $org->id,
            'user_id' => $creator->id,
        ]);

        BetOption::factory()->create(['bet_id' => $bet->id, 'title' => 'Visible Option']);

        $this->browse(function (Browser $browser) use ($viewer, $bet) {
            $browser->formLoginAs($viewer)
                ->visitRoute('bets.detail', ['bet' => $bet->slugUrl()])
                ->waitFor('#bet-detail-page')
                ->assertSee($bet->title)
                ->assertSee('Visible Option');
        });
    }
}
