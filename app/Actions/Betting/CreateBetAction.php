<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\Enums\BetStatus;
use App\Models\Bet;
use App\Models\User;

class CreateBetAction
{
    public function execute(User $creator, string $title, string|null $description = null, int|null $expiresInMinutes = null): Bet
    {
        $expiresAt = null;
        if ($expiresInMinutes !== null) {
            $expiresAt = now()->addMinutes($expiresInMinutes);
        }

        return Bet::create([
            'creator_id' => $creator->id,
            'title' => $title,
            'description' => $description,
            'status' => BetStatus::Open,
            'expires_at' => $expiresAt,
        ]);
    }
}

