<?php

declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

final class UserBetPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_any_user_bet(): void
    {
        $admin = User::factory()->admin()->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        $userBet = UserBet::factory()->create([
            'user_id' => User::factory()->create()->id,
            'bet_option_id' => $option->id,
        ]);

        $this->assertTrue($admin->can('viewUserBet', $userBet));
    }

    public function test_bet_owner_can_view_own_bet(): void
    {
        $owner = User::factory()->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        $userBet = UserBet::factory()->create([
            'user_id' => $owner->id,
            'bet_option_id' => $option->id,
        ]);

        $this->assertTrue($owner->can('viewUserBet', $userBet));
    }

    public function test_other_user_cannot_view_bet(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $bet = Bet::factory()->create(['user_id' => User::factory()->create()->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);
        $userBet = UserBet::factory()->create([
            'user_id' => $owner->id,
            'bet_option_id' => $option->id,
        ]);

        $this->assertFalse($other->can('viewUserBet', $userBet));
    }
}
