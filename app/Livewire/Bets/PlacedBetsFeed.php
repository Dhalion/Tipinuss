<?php declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Models\Bet;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class PlacedBetsFeed extends Component
{
    public Bet $bet;

    #[On('refresh-placed-bets')]
    public function refreshFeed(): void
    {
        // Trigger re-render via wire:poll or manual refresh
    }

    public function render(): View
    {
        $placedBets = $this->bet
            ->userBets()
            ->with('user', 'betOption')
            ->latest()
            ->limit(20)
            ->get();

        return view('livewire.bets.placed-bets-feed', compact('placedBets'));
    }
}

