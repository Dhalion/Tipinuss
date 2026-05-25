<?php

declare(strict_types=1);

namespace Tests\Integration\Listeners;

use App\Enums\BetStatus;
use App\Enums\UserBetStatus;
use App\Events\Betting\BetClosed;
use App\Listeners\Betting\NotifyBetParticipants;
use App\Models\Bet;
use App\Models\BetOption;
use App\Models\User;
use App\Notifications\BetClosed as BetClosedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Notification;

final class NotifyBetParticipantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifies_all_participants(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $winner = User::factory()->withBalance(500)->create();
        $loser = User::factory()->withBalance(300)->create();

        $bet = Bet::factory()->create(['user_id' => $admin->id, 'status' => BetStatus::Closed]);
        $winningOption = BetOption::factory()->create(['bet_id' => $bet->id]);
        $losingOption = BetOption::factory()->create(['bet_id' => $bet->id]);

        $participants = [
            [
                'user' => $winner,
                'status' => UserBetStatus::Won->value,
                'wagered' => 100,
                'winnings' => 200,
            ],
            [
                'user' => $loser,
                'status' => UserBetStatus::Lost->value,
                'wagered' => 50,
                'winnings' => 0,
            ],
        ];

        $event = new BetClosed(bet: $bet, participants: $participants);
        $listener = app(NotifyBetParticipants::class);

        $listener->handle($event);

        Notification::assertSentTo(
            [$winner, $loser],
            BetClosedNotification::class,
        );
    }

    public function test_deduplicates_notifications_for_same_user(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $multiBettor = User::factory()->withBalance(500)->create();

        $bet = Bet::factory()->create(['user_id' => $admin->id, 'status' => BetStatus::Closed]);
        $winningOption = BetOption::factory()->create(['bet_id' => $bet->id]);
        $losingOption = BetOption::factory()->create(['bet_id' => $bet->id]);

        $participants = [
            [
                'user' => $multiBettor,
                'status' => UserBetStatus::Won->value,
                'wagered' => 100,
                'winnings' => 200,
            ],
            [
                'user' => $multiBettor,
                'status' => UserBetStatus::Lost->value,
                'wagered' => 50,
                'winnings' => 0,
            ],
        ];

        $event = new BetClosed(bet: $bet, participants: $participants);
        $listener = app(NotifyBetParticipants::class);

        $listener->handle($event);

        Notification::assertSentTo(
            [$multiBettor],
            BetClosedNotification::class,
            fn ($notification, $channels, $notifiable) => true,
            1,
        );
    }
}
