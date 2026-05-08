<?php

declare(strict_types=1);

namespace App\Livewire\Page\Bets;

use App\Actions\Betting\CloseBetAction;
use App\Actions\Betting\DeleteBetAction;
use App\Actions\Betting\PlaceBetAction;
use App\DTOs\Betting\PlaceBetData;
use App\Exceptions\BetException;
use App\Models\Bet;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Services\Betting\BetCalculationService;
use App\Services\Betting\BettingValidationService;
use App\Services\MetaTagService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class BetDetail extends Component
{
    public Bet $bet;

    public bool $showCloseBetModal = false;

    public bool $showDeleteBetModal = false;

    public function __construct(
        private BetOptionRepositoryInterface $betOptions,
        private UserBetRepositoryInterface $userBets,
    ) {}

    public function mount(Bet $bet, MetaTagService $metaTagService): void
    {
        $this->bet = $bet->load('creator', 'betOptions', 'userBets');

        $metaTagService->setBetMetaTags(
            title: $this->bet->title,
            description: $this->bet->description ?? 'Tippe auf '.$this->bet->title.' und verdiene Waschnüsse auf Tipinuss',
        );
    }

    #[Computed]
    public function canCloseBet(): bool
    {
        if (! $this->bet->isOpen()) {
            return false;
        }

        $user = auth()->user();

        if ($user !== null && $user->isAdmin()) {
            return true;
        }

        return $this->userBets->countDistinctBettorsForBet($this->bet) >= 2;
    }

    public function openCloseBetModal(): void
    {
        $this->authorize('closeBet', $this->bet);
        $this->showCloseBetModal = true;
    }

    public function closeCloseBetModal(): void
    {
        $this->showCloseBetModal = false;
    }

    public function openDeleteBetModal(): void
    {
        $this->authorize('deleteBet', $this->bet);
        $this->showDeleteBetModal = true;
    }

    public function closeDeleteBetModal(): void
    {
        $this->showDeleteBetModal = false;
    }

    public function placeBet(
        string $optionId,
        int|float $amount,
        PlaceBetAction $action,
        BetCalculationService $calculation,
    ): void {
        $user = auth()->user();
        if ($user === null) {
            return;
        }

        $validated = validator(
            ['amount' => $amount],
            ['amount' => ['required', 'numeric', 'min:'.BettingValidationService::MIN_BET_AMOUNT, 'max:'.BettingValidationService::MAX_BET_AMOUNT]],
            [
                'amount.required' => __('validation.required', ['attribute' => __('bets.stake_amount')]),
                'amount.numeric' => __('validation.numeric', ['attribute' => __('bets.stake_amount')]),
                'amount.min' => __('validation.min.numeric', ['attribute' => __('bets.stake_amount'), 'min' => BettingValidationService::MIN_BET_AMOUNT]),
                'amount.max' => __('validation.max.numeric', ['attribute' => __('bets.stake_amount'), 'max' => BettingValidationService::MAX_BET_AMOUNT]),
            ],
        )->validate();

        try {
            $option = $this->betOptions->findById($optionId);
            if ($option === null) {
                throw new BetException('Diese Option wurde nicht gefunden.');
            }

            $data = PlaceBetData::make(user: $user, option: $option, amount: (int) $validated['amount']);
            $action->execute($data);

            $winnings = $calculation->calculatePotentialWinnings($option, (int) $validated['amount']);
            session()->flash('success', '🎉 Wette erfolgreich platziert! Möglicher Gewinn: '.number_format($winnings, 0).' 🌰');

            $this->dispatch('bet-placed');
            $this->dispatch('refresh-placed-bets');
        } catch (BetException $exception) {
            $this->addError('amount', $exception->getMessage());
        }
    }

    public function executeCloseBet(string $winningOptionId, CloseBetAction $action): void
    {
        $this->authorize('closeBet', $this->bet);

        if (! $this->canCloseBet()) {
            $this->addError('closeBet', __('bets.close_not_allowed'));

            return;
        }

        $action->execute($this->bet, $winningOptionId);

        session()->flash('success', '✓ Wette erfolgreich geschlossen und Gewinne ausgezahlt.');
        $this->showCloseBetModal = false;
        $this->redirect(route('bets.list'), navigate: true);
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
        return view('pages.bets.detail', [
            'canCloseBet' => $this->canCloseBet(),
        ]);
    }
}
