<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Bet;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

final class BetClosed extends Notification
{
    use Queueable;

    public function __construct(
        public readonly Bet $bet,
        public readonly string $userBetStatus,
        public readonly int $amountWagered,
        public readonly int $potentialWinnings,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'bet_id' => $this->bet->id,
            'bet_title' => $this->bet->title,
            'bet_slug_url' => $this->bet->slugUrl(),
            'user_bet_status' => $this->userBetStatus,
            'amount_wagered' => $this->amountWagered,
            'potential_winnings' => $this->potentialWinnings,
        ];
    }
}
