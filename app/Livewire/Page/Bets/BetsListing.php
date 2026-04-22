<?php

namespace App\Livewire\Page\Bets;

use App\Enums\BetStatus;
use App\Models\Bet;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class BetsListing extends Component
{
    use WithPagination;

    #[On('bet-created')]
    #[On('bet-updated')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $bets = Bet::with('creator', 'betOptions', 'userBets')
            ->where('status', '!=', BetStatus::Closed->value)
            ->latest()
            ->paginate(15);

        return view('pages.bets.listing', ['bets' => $bets]);
    }
}
