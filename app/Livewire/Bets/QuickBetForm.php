<?php declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Models\BetOption;
use Livewire\Attributes\Validate;
use Livewire\Component;

class QuickBetForm extends Component
{
    public BetOption $option;
    public int $betId;

    #[Validate('required|numeric|min:1')]
    public int $amount = 0;

    public function placeBet(): void
    {
        $this->validate();

        // TODO: Implement bet placement
        // - Create UserBet record
        // - Deduct from user soapnuts
        // - Handle insufficient balance
        // - Flash success
        // - Close modal/reset form
    }

    public function render()
    {
        return view('livewire.bets.quick-bet-form');
    }
}
