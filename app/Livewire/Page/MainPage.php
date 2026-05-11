<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class MainPage extends Component
{
    public function render(BetRepositoryInterface $bets): View
    {
        return view('pages.main-page', [
            'recentBets' => $bets->recentOpenForUser(auth()->user()),
        ]);
    }
}
