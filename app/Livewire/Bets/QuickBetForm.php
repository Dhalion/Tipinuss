<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Models\BetOption;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class QuickBetForm extends Component
{
    public BetOption $option;

    #[Validate('required|numeric|min:1')]
    public int $amount = 0;

    public function render(): View
    {
        return view('livewire.bets.quick-bet-form');
    }
}
