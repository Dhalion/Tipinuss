<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Leaderboard extends Component
{
    public function render(UserRepositoryInterface $users): View
    {
        return view('livewire.bets.leaderboard', [
            'topBettors' => $users->topBySoapnuts(),
        ]);
    }
}
