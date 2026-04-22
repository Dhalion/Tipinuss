<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\Enums\BetStatus;
use App\Models\Bet;

class CloseBetAction
{
    public function execute(Bet $bet): Bet
    {
        $bet->update(['status' => BetStatus::Closed]);

        return $bet->refresh();
    }
}

