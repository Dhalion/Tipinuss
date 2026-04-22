<?php

declare(strict_types=1);

namespace App\Livewire\Page\Bets;

use App\Actions\Betting\CloseBetAction;
use App\Actions\Betting\DeleteBetAction;
use App\Models\Bet;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BetDetail extends Component
{
    public Bet $bet;

    public function mount(Bet $bet): void
    {
        $this->bet = $bet->load('creator', 'betOptions', 'userBets.user');
    }

    public function selectOption(int $optionId, string $optionTitle, float $odds): void
    {
        $this->dispatch('showModal', modalName: "place-bet-{$optionId}");
    }

    public function closeBet(CloseBetAction $action): void
    {
        $this->authorize('closeBet', $this->bet);

        $action->execute($this->bet);

        session()->flash('success', '✓ Wette erfolgreich geschlossen.');
        $this->dispatch('refresh-placed-bets');
    }

    public function deleteBet(DeleteBetAction $action): void
    {
        $this->authorize('deleteBet', $this->bet);

        $betTitle = $this->bet->title;
        $action->execute($this->bet);

        session()->flash('success', "🗑️ Wette \"{$betTitle}\" wurde gelöscht.");
        $this->redirect(route('bets.list'), navigate: true);
    }

    public function render(): View
    {
        return view('pages.bets.detail');
    }
}
