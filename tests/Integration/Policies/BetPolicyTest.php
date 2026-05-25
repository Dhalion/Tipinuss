<?php

declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Models\Bet;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class BetPolicyTest extends TestCase
{
    use RefreshDatabase;

    // --- closeBet ---

    public function test_close_bet_admin_can_close_any_open_bet(): void
    {
        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->assertTrue($admin->can('closeBet', $bet));
    }

    public function test_close_bet_creator_can_close_own_open_bet(): void
    {
        $creator = User::factory()->create();
        $bet = Bet::factory()->create(['user_id' => $creator->id]);

        $this->assertTrue($creator->can('closeBet', $bet));
    }

    public function test_close_bet_other_user_cannot_close(): void
    {
        $other = User::factory()->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->assertFalse($other->can('closeBet', $bet));
    }

    public function test_close_bet_cannot_close_closed_bet(): void
    {
        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->closed()->create();

        $this->assertFalse($admin->can('closeBet', $bet));
    }

    // --- deleteBet ---

    public function test_delete_bet_admin_can_delete_any_open_bet(): void
    {
        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->assertTrue($admin->can('deleteBet', $bet));
    }

    public function test_delete_bet_creator_can_delete_own_open_bet(): void
    {
        $creator = User::factory()->create();
        $bet = Bet::factory()->create(['user_id' => $creator->id]);

        $this->assertTrue($creator->can('deleteBet', $bet));
    }

    public function test_delete_bet_other_user_cannot_delete(): void
    {
        $other = User::factory()->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->assertFalse($other->can('deleteBet', $bet));
    }

    public function test_delete_bet_cannot_delete_closed_bet(): void
    {
        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->closed()->create();

        $this->assertFalse($admin->can('deleteBet', $bet));
    }

    // --- viewBet ---

    public function test_view_bet_admin_sees_all(): void
    {
        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->create();

        $this->assertTrue($admin->can('viewBet', $bet));
    }

    public function test_view_bet_same_organisation_can_view(): void
    {
        $organisation = Organisation::factory()->create();
        $user = User::factory()->create(['organisation_id' => $organisation->id]);
        $bet = Bet::factory()->create(['organisation_id' => $organisation->id]);

        $this->assertTrue($user->can('viewBet', $bet));
    }

    public function test_view_bet_different_organisation_cannot_view(): void
    {
        $orgA = Organisation::factory()->create();
        $orgB = Organisation::factory()->create();
        $user = User::factory()->create(['organisation_id' => $orgA->id]);
        $bet = Bet::factory()->create(['organisation_id' => $orgB->id]);

        $this->assertFalse($user->can('viewBet', $bet));
    }
}
