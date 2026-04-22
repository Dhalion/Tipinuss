<?php

declare(strict_types=1);

namespace App\Livewire\Bets;

use App\Actions\Betting\PlaceBetAction;
use App\Exceptions\BetException;
use App\Models\BetOption;
use App\Services\Betting\BetCalculationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PlaceBetModal extends Component
{
    public ?string $optionId = null;
    public string $optionTitle = '';
    public float $odds = 0;

    #[Validate('required|numeric|min:1|max:100000')]
    public int $amount = 0;

    public function mount(string $optionId, string $optionTitle, float $odds): void
    {
        $this->optionId = $optionId;
        $this->optionTitle = $optionTitle;
        $this->odds = $odds;
    }

    public function closeModal(): void
    {
        $this->dispatch('closeModal', modalName: "place-bet-{$this->optionId}");
    }

    public function placeBet(PlaceBetAction $action, BetCalculationService $calculation): void
    {
        $this->validate();

        $user = auth()->user();
        if ($user === null) {
            session()->flash('error', 'Du musst angemeldet sein um zu wetten.');
            $this->closeModal();
            return;
        }

        try {
            $option = BetOption::find($this->optionId);
            if ($option === null) {
                throw new BetException('Diese Option wurde nicht gefunden.');
            }

            $userBet = $action->execute($user, $option, $this->amount);
            $winnings = $calculation->calculatePotentialWinnings($option, $this->amount);

            session()->flash('success', "🎉 Wette erfolgreich platziert! Möglicher Gewinn: " . number_format($winnings, 0) . " 🌰");

            $this->amount = 0;
            $this->resetErrorBag();
            $this->dispatch('refresh-placed-bets');
            $this->closeModal();
        } catch (BetException $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.bets.place-bet-modal');
    }
}




