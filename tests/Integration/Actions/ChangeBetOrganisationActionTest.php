<?php

declare(strict_types=1);

namespace Tests\Integration\Actions;

use App\Actions\Admin\ChangeBetOrganisationAction;
use App\Models\Bet;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class ChangeBetOrganisationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_changes_bet_organisation(): void
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        $bet = Bet::factory()->create(['user_id' => $user->id, 'organisation_id' => null]);
        $action = app(ChangeBetOrganisationAction::class);

        $action->execute($bet, $organisation);

        $this->assertSame($organisation->id, $bet->fresh()->organisation_id);
    }

    public function test_removes_bet_organisation(): void
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        $bet = Bet::factory()->create(['user_id' => $user->id, 'organisation_id' => $organisation->id]);
        $action = app(ChangeBetOrganisationAction::class);

        $action->execute($bet, null);

        $this->assertNull($bet->fresh()->organisation_id);
    }
}
