<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Enums\TransactionType;
use App\Models\BalanceTransaction;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class AccountPageTest extends DuskTestCase
{
    public function test_user_sees_account_stats(): void
    {
        $user = User::factory()->withBalance(5000)->create([
            'name' => 'Test User',
        ]);

        $bet1 = Bet::factory()->create(['user_id' => $user->id]);
        $option1 = BetOption::factory()->create(['bet_id' => $bet1->id]);
        UserBet::factory()->won()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option1->id,
        ]);

        $bet2 = Bet::factory()->create(['user_id' => $user->id]);
        $option2 = BetOption::factory()->create(['bet_id' => $bet2->id]);
        UserBet::factory()->lost()->create([
            'user_id' => $user->id,
            'bet_option_id' => $option2->id,
        ]);

        BalanceTransaction::create([
            'user_id' => $user->id,
            'type' => TransactionType::Initial,
            'amount' => 5000,
            'balance_after' => 5000,
            'description' => 'Startguthaben bei Registrierung',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->formLoginAs($user)
                ->visit(route('account'))
                ->waitFor('#account-page')
                ->assertSeeIn('#account-balance', '5,000')
                ->assertSee('Test User')
                ->assertSee('Wetten insgesamt')
                ->assertSee('Gewonnen')
                ->assertSee('Verloren')
                ->assertSee('Aktivität');
        });
    }
}
