<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Repositories\Contracts\BetOptionRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class QuickBetForm extends Component
{
    #[Locked]
    public string $optionId;

    #[Validate('required|numeric|min:1')]
    public int $amount = 0;

    public function mount(string $optionId): void
    {
        $this->optionId = $optionId;
    }

    public function render(BetOptionRepositoryInterface $betOptions): View
    {
        $option = $betOptions->findById($this->optionId);

        return view('livewire.bets.quick-bet-form', [
            'option' => $option,
        ]);
    }
}
