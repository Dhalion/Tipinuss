<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

final class DeleteBetTest extends DuskTestCase
{
    public function test_bet_creator_can_delete_bet_and_bettors_get_refunded(): void
    {
        $creator = User::factory()->create();
        $bettor = User::factory()->withBalance(500)->create();

        $bet = Bet::factory()->create(['user_id' => $creator->id]);
        $option = BetOption::factory()->create(['bet_id' => $bet->id, 'title' => 'Yes']);

        UserBet::factory()->create([
            'user_id' => $bettor->id,
            'bet_option_id' => $option->id,
            'amount_wagered' => 100,
            'potential_winnings' => 200,
        ]);

        $bettorBalanceBefore = $bettor->fresh()->soapnuts;

        $this->browse(function (Browser $browser) use ($creator, $bet) {
            $browser->formLoginAs($creator)
                ->visitRoute('bets.detail', ['bet' => $bet->slugUrl()])
                ->waitFor('#bet-detail-page')
                ->click('#bet-delete-'.$bet->id)
                ->waitFor('#bet-delete-modal')
                ->check('#bet-refund-checkbox')
                ->click('#bet-delete-confirm')
                ->waitFor('[data-flux-toast-dialog][data-variant="success"]')
                ->assertSee('wurde gelöscht');
        });

        $this->assertDatabaseMissing('bets', ['id' => $bet->id]);
        $this->assertDatabaseMissing('bet_options', ['id' => $option->id]);

        $this->assertSame($bettorBalanceBefore + 100, $bettor->fresh()->soapnuts);
    }
}
