<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class SoapnutsBalance extends Component
{
    #[On('bet-placed')]
    public function refresh(): void {}

    public function render(): View
    {
        return view('livewire.soapnuts-balance');
    }
}
