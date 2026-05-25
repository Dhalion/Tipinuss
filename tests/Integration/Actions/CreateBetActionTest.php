<?php

declare(strict_types=1);

namespace Tests\Integration\Actions;

use App\Actions\Betting\CreateBetAction;
use App\DTOs\Betting\BetOptionData;
use App\DTOs\Betting\CreateBetData;
use App\Enums\BetStatus;
use App\Models\Bet;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class CreateBetActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_bet_with_options(): void
    {
        $user = User::factory()->create();
        $action = app(CreateBetAction::class);

        $bet = $action->execute(new CreateBetData(
            creator: $user,
            title: 'Wer gewinnt das Turnier?',
            description: 'Ein spannender Wettkampf.',
            expiresAt: null,
            options: [
                BetOptionData::make('Team A', 2.50),
                BetOptionData::make('Team B', 1.80),
                BetOptionData::make('Team C', 3.20),
            ],
            organisationId: null,
        ));

        $this->assertInstanceOf(Bet::class, $bet);
        $this->assertSame('Wer gewinnt das Turnier?', $bet->title);
        $this->assertSame('wer-gewinnt-das-turnier', $bet->slug);
        $this->assertSame(BetStatus::Open, $bet->status);
        $this->assertSame($user->id, $bet->user_id);
        $this->assertNull($bet->organisation_id);

        $this->assertDatabaseHas('bets', ['id' => $bet->id]);
        $this->assertDatabaseCount('bet_options', 3);
    }

    public function test_generates_unique_slug_on_collision(): void
    {
        $user = User::factory()->create();
        $action = app(CreateBetAction::class);

        Bet::factory()->create(['slug' => 'test-bet']);

        $bet = $action->execute(new CreateBetData(
            creator: $user,
            title: 'Test Bet',
            description: null,
            expiresAt: null,
            options: [
                BetOptionData::make('Option 1', 2.0),
                BetOptionData::make('Option 2', 1.5),
            ],
            organisationId: null,
        ));

        $this->assertSame('test-bet-1', $bet->slug);
    }

    public function test_falls_back_to_random_slug_after_many_collisions(): void
    {
        $user = User::factory()->create();
        $action = app(CreateBetAction::class);

        for ($i = 0; $i < 100; $i++) {
            Bet::factory()->create(['slug' => $i === 0 ? 'busy' : 'busy-'.$i]);
        }

        $bet = $action->execute(new CreateBetData(
            creator: $user,
            title: 'busy',
            description: null,
            expiresAt: null,
            options: [
                BetOptionData::make('Option 1', 2.0),
                BetOptionData::make('Option 2', 1.5),
            ],
            organisationId: null,
        ));

        $this->assertStringStartsWith('busy-', $bet->slug);
        $this->assertStringNotMatchesFormat('busy-%d', $bet->slug);
    }

    public function test_creates_bet_with_organisation_scope(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create(['organisation_id' => $organisation->id]);
        $action = app(CreateBetAction::class);

        $bet = $action->execute(new CreateBetData(
            creator: $user,
            title: 'Org Bet',
            description: null,
            expiresAt: null,
            options: [
                BetOptionData::make('Yes', 2.0),
                BetOptionData::make('No', 1.5),
            ],
            organisationId: $organisation->id,
        ));

        $this->assertSame($organisation->id, $bet->organisation_id);
    }
}
