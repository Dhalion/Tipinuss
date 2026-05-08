<?php

declare(strict_types=1);

namespace App\Livewire\Page\Bets;

use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

final class BetsListing extends Component
{
    use WithPagination;

    #[On('bet-created')]
    #[On('bet-updated')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function render(BetRepositoryInterface $bets): View
    {
        return view('pages.bets.listing', [
            'bets' => $bets->paginateOpen(),
        ]);
    }
}
