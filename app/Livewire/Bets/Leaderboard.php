<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class Leaderboard extends Component
{
    public function render(UserRepositoryInterface $users): View
    {
        $user = Auth::user();

        if ($user === null || $user->isAdmin()) {
            $organisationId = null;
        } else {
            $organisationId = $user->organisation_id;
        }

        return view('livewire.bets.leaderboard', [
            'topBettors' => $users->topBySoapnuts(organisationId: $organisationId),
        ]);
    }
}
