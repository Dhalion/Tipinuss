<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Models\Bet;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class PlacedBetsFeed extends Component
{
    public Bet $bet;

    #[On('refresh-placed-bets')]
    public function refreshFeed(): void {}

    public function render(UserBetRepositoryInterface $userBets): View
    {
        return view('livewire.bets.placed-bets-feed', [
            'placedBets' => $userBets->recentForBet($this->bet),
        ]);
    }
}
