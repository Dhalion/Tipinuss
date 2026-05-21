<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

final class PlacedBetsFeed extends Component
{
    #[Locked]
    public string $betId;

    public int $lastKnownCount = 0;

    public function mount(string $betId): void
    {
        $this->betId = $betId;
    }

    #[On('refresh-placed-bets')]
    public function refreshFeed(): void
    {
        $this->dispatchCountChanged();
    }

    public function dispatchCountChanged(): void
    {
        $bet = app(BetRepositoryInterface::class)->findById($this->betId);
        if ($bet === null) {
            return;
        }

        $currentCount = app(UserBetRepositoryInterface::class)->recentForBet($bet)->count();

        if ($currentCount !== $this->lastKnownCount && $this->lastKnownCount > 0) {
            $this->dispatch('bets-count-changed');
        }

        $this->lastKnownCount = $currentCount;
    }

    public function render(UserBetRepositoryInterface $userBets, BetRepositoryInterface $bets): View
    {
        $bet = $bets->findById($this->betId);

        $placedBets = $bet !== null ? $userBets->recentForBet($bet) : collect();
        $this->lastKnownCount = $placedBets->count();

        return view('livewire.bets.placed-bets-feed', [
            'placedBets' => $placedBets,
            'betIsClosed' => $bet !== null && ! $bet->isOpen(),
        ]);
    }
}
