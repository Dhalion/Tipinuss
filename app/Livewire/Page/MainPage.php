<?php

declare(strict_types=1);

namespace App\Livewire\Page;

use App\Repositories\Contracts\BetRepositoryInterface;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

final class MainPage extends Component
{
    public function mount(): void
    {
        /** @var array{balance: int, message: string|null}|null $toast */
        $toast = Session::pull('_registration_toast');

        if ($toast === null) {
            return;
        }

        $text = __('auth.welcome_toast_balance', ['balance' => $toast['balance']]);

        if ($toast['message'] !== null) {
            $text .= "\n".$toast['message'];
        }

        Flux::toast(
            heading: __('auth.welcome_toast_heading'),
            text: $text,
            variant: 'success',
            duration: 6000,
        );
    }

    public function render(BetRepositoryInterface $bets): View
    {
        return view('pages.main-page', [
            'recentBets' => $bets->recentOpenForUser(auth()->user()),
        ]);
    }
}
