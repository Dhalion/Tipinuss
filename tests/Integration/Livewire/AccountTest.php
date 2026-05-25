<?php

declare(strict_types=1);

namespace Tests\Integration\Livewire;

use App\Livewire\Page\Account;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

final class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_account_page(): void
    {
        $user = User::factory()->withBalance(1000)->create();

        Livewire::actingAs($user)
            ->test(Account::class)
            ->assertOk();
    }

    public function test_shows_correct_stat_counts(): void
    {
        $user = User::factory()->withBalance(1000)->create();
        $bet = Bet::factory()->create();
        $option = BetOption::factory()->create(['bet_id' => $bet->id]);

        UserBet::factory()->won()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
        ]);
        UserBet::factory()->lost()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
        ]);
        UserBet::factory()->pending()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option->id,
        ]);

        Livewire::actingAs($user)
            ->test(Account::class)
            ->assertOk();
    }
}
