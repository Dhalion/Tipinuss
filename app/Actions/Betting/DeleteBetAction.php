<?php

declare(strict_types=1);

namespace App\Actions\Betting;

use App\Models\Bet;
use App\Repositories\Contracts\BetRepositoryInterface;

final class DeleteBetAction
{
    public function __construct(
        private BetRepositoryInterface $bets,
    ) {}

    public function execute(Bet $bet): void
    {
        $this->bets->delete($bet);
    }
}
