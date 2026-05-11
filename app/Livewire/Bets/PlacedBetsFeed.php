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

    public int $lastKnownCount = 0;

    #[On('refresh-placed-bets')]
    public function refreshFeed(): void
    {
        $this->dispatchCountChanged();
    }

    public function dispatchCountChanged(): void
    {
        $currentCount = app(UserBetRepositoryInterface::class)->recentForBet($this->bet)->count();

        if ($currentCount !== $this->lastKnownCount && $this->lastKnownCount > 0) {
            $this->dispatch('bets-count-changed');
        }

        $this->lastKnownCount = $currentCount;
    }

    public function render(UserBetRepositoryInterface $userBets): View
    {
        $placedBets = $userBets->recentForBet($this->bet);
        $this->lastKnownCount = $placedBets->count();

        return view('livewire.bets.placed-bets-feed', [
            'placedBets' => $placedBets,
            'betIsClosed' => ! $this->bet->isOpen(),
        ]);
    }
}
