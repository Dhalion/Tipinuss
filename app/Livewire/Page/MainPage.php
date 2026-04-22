<?php declare(strict_types=1);

namespace App\Livewire\Page;

use App\Models\Bet;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MainPage extends Component
{
    public function render(): View
    {
        $recentBets = Bet::with('creator', 'betOptions', 'userBets.user')
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.main-page', ['recentBets' => $recentBets]);
    }
}

