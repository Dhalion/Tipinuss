<?php

declare(strict_types=1);

namespace App\Listeners\Betting;

use App\Events\Betting\BetClosed;
use App\Notifications\BetClosed as BetClosedNotification;
use Illuminate\Support\Facades\Log;

final class NotifyBetParticipants
{
    public function handle(BetClosed $event): void
    {
        $notifiedUserIds = [];

        foreach ($event->participants as $participant) {
            $userId = $participant['user']->id;

            if (isset($notifiedUserIds[$userId])) {
                continue;
            }

            $notifiedUserIds[$userId] = true;

            try {
                $participant['user']->notify(new BetClosedNotification(
                    bet: $event->bet,
                    userBetStatus: $participant['status'],
                    amountWagered: $participant['wagered'],
                    potentialWinnings: $participant['winnings'],
                ));
            } catch (\Throwable $e) {
                Log::warning('Failed to notify participant of closed bet', [
                    'bet_id' => $event->bet->id,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
