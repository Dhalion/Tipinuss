<?php

declare(strict_types=1);

namespace App\Livewire\Page\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

final class PendingApproval extends Component
{
    public function checkStatus(): void
    {
        if (auth()->user()->isApproved()) {
            $this->redirect(route('main'), navigate: true);
        }
    }

    public function render(): View
    {
        return view('pages.pending-approval');
    }
}
