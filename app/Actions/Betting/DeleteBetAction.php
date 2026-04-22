<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\Models\Bet;

class DeleteBetAction
{
    public function execute(Bet $bet): void
    {
        $bet->delete();
    }
}
