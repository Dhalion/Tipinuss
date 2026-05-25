<?php

declare(strict_types=1);

namespace App\Events\Betting;

use App\Models\Bet;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

final class BetClosed
{
    use Dispatchable;

    /**
     * @param  list<array{user: User, status: string, wagered: int, winnings: int}>  $participants
     */
    public function __construct(
        public readonly Bet $bet,
        public readonly array $participants,
    ) {}
}
