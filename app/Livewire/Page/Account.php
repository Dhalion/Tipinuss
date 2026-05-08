<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use App\Repositories\Contracts\UserBetRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Account extends Component
{
    public function render(UserBetRepositoryInterface $userBets): View
    {
        $user = auth()->user();

        return view('pages.account', [
            'userBets' => $user !== null ? $userBets->recentForUser($user) : collect(),
        ]);
    }
}
