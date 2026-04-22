<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Leaderboard extends Component
{
    public function render(): View
    {
        $topBettors = User::withCount('userBets')
            ->orderByDesc('soapnuts')
            ->take(10)
            ->get();

        return view('livewire.bets.leaderboard', [
            'topBettors' => $topBettors,
        ]);
    }
}
