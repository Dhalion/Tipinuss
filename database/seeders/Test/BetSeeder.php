<?php

declare(strict_types=1);

namespace Database\Seeders\Test;

use App\Enums\BetStatus;
use App\Enums\UserBetStatus;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Models\UserBet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class BetSeeder extends Seeder
{
    public function run(): void
    {
        /** @var User $admin */
        $admin = User::where('email', 'admin@test.com')->first();
        /** @var User $alice */
        $alice = User::where('email', 'alice@test.com')->first();

        $openBet = Bet::create([
            'title' => 'Wer gewinnt das Turnier?',
            'slug' => Str::slug('Wer gewinnt das Turnier'),
            'description' => 'Ein spannender Wettkampf steht bevor.',
            'user_id' => $admin->id,
            'status' => BetStatus::Open,
            'expires_at' => Carbon::now()->addDays(7),
        ]);

        $optionA = BetOption::create([
            'bet_id' => $openBet->id,
            'title' => 'Team Alpha',
            'odds' => 2.50,
            'base_odds' => 2.50,
        ]);

        $optionB = BetOption::create([
            'bet_id' => $openBet->id,
            'title' => 'Team Beta',
            'odds' => 1.80,
            'base_odds' => 1.80,
        ]);

        $closedBet = Bet::create([
            'title' => 'Championship Finale',
            'slug' => Str::slug('Championship Finale'),
            'description' => 'Das große Finale ist entschieden.',
            'user_id' => $admin->id,
            'status' => BetStatus::Closed,
            'closed_at' => Carbon::now()->subDay(),
        ]);

        $winnerOption = BetOption::create([
            'bet_id' => $closedBet->id,
            'title' => 'Team Gamma',
            'odds' => 3.00,
            'base_odds' => 3.00,
            'result' => true,
        ]);

        $loserOption = BetOption::create([
            'bet_id' => $closedBet->id,
            'title' => 'Team Delta',
            'odds' => 2.20,
            'base_odds' => 2.20,
            'result' => false,
        ]);

        UserBet::create([
            'user_id' => $alice->id,
            'bet_option_id' => $winnerOption->id,
            'amount_wagered' => 100,
            'potential_winnings' => 300,
            'status' => UserBetStatus::Won,
        ]);

        UserBet::create([
            'user_id' => $alice->id,
            'bet_option_id' => $loserOption->id,
            'amount_wagered' => 50,
            'potential_winnings' => 110,
            'status' => UserBetStatus::Lost,
        ]);
    }
}
