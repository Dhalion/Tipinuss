<?php

declare(strict_types=1);

namespace App\Livewire\Page\Bets;

use App\Actions\Admin\ChangeBetOrganisationAction;
use App\Actions\Betting\CloseBetAction;
use App\Actions\Betting\DeleteBetAction;
use App\Actions\Betting\PlaceBetAction;
use App\DTOs\Betting\CloseBetData;
use App\DTOs\Betting\PlaceBetData;
use App\Exceptions\BetException;
use App\Models\Bet;
use App\Models\BetOption;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Services\Betting\BetCalculationService;
use App\Services\Betting\BettingValidationService;
use App\Services\MetaTagService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

final class BetDetail extends Component
{
    #[Locked]
    public string $betId;

    public function mount(Bet $bet, MetaTagService $metaTagService, BetRepositoryInterface $bets): void
    {
        $this->betId = $bet->id;

        $this->authorize('viewBet', $bets->findByIdOrFail($this->betId));

        $metaTagService->setBetMetaTags(
            title: $bet->title,
            description: $bet->description,
        );
    }

    #[Computed]
    public function bet(): Bet
    {
        return app(BetRepositoryInterface::class)->findByIdOrFail($this->betId);
    }

    #[Computed]
    public function canCloseBet(): bool
    {
        $bet = $this->bet();
        $user = auth()->user();

        if ($user === null) {
            return false;
        }

        $distinctBettorCount = app(UserBetRepositoryInterface::class)->countDistinctBettorsForBet($bet);

        return app(BettingValidationService::class)->canCloseBet($bet, $user, $distinctBettorCount);
    }

    #[On('bets-count-changed')]
    public function refreshCanClose(): void {}

    public function placeBet(
        string $optionId,
        int|float $amount,
        PlaceBetAction $action,
        BetCalculationService $calculation,
        BetOptionRepositoryInterface $betOptions,
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
            $option = $betOptions->findById($optionId);
            if ($option === null) {
                throw BetException::optionNotFound();
            }

            $data = PlaceBetData::make(user: $user, option: $option, amount: (int) $validated['amount']);
            $action->execute($data);

            $winnings = $calculation->calculatePotentialWinnings($option, (int) $validated['amount']);
            Flux::toast(variant: 'success', heading: __('bets.placed_heading'), text: __('bets.potential_winnings_text', ['amount' => number_format($winnings, 0)]));

            $this->dispatch('bet-placed');
            $this->dispatch('refresh-placed-bets');
        } catch (BetException $exception) {
            $this->addError('amount', $exception->getMessage());
        }
    }

    public function executeCloseBet(string $winningOptionId, CloseBetAction $action): void
    {
        $bet = $this->bet();

        $this->authorize('closeBet', $bet);

        if (! $this->canCloseBet()) {
            $this->addError('closeBet', __('bets.close_not_allowed'));

            return;
        }

        $action->execute(CloseBetData::make(bet: $bet, winningOptionId: $winningOptionId));

        Flux::toast(variant: 'success', text: __('bets.closed_success'));
        $this->redirect(route('bets.list'), navigate: true);
    }

    public function deleteBet(DeleteBetAction $action): void
    {
        $bet = $this->bet();

        $this->authorize('deleteBet', $bet);

        $betTitle = $bet->title;
        $action->execute($bet);

        Flux::toast(variant: 'success', text: __('bets.deleted_success', ['title' => $betTitle]));
        $this->redirect(route('bets.list'), navigate: true);
    }

    public function changeOrganisation(
        ?string $organisationId,
        ChangeBetOrganisationAction $action,
        OrganisationRepositoryInterface $organisations,
    ): void {
        $this->authorize('admin');

        $organisation = $organisationId !== null && $organisationId !== ''
            ? $organisations->findById($organisationId)
            : null;

        if ($organisationId !== null && $organisationId !== '' && $organisation === null) {
            return;
        }

        $bet = $this->bet();
        $action->execute($bet, $organisation);
    }

    /** @return Collection<int, BetOption> */
    #[Computed]
    public function optionsByOdds(): Collection
    {
        return $this->bet()->betOptions->sortByDesc('odds');
    }

    /** @return Collection<int, BetOption> */
    #[Computed]
    public function optionsByBets(): Collection
    {
        return $this->bet()->betOptions->sortByDesc(fn (BetOption $option): int => $option->userBets->count());
    }

    public function render(OrganisationRepositoryInterface $organisations): View
    {
        return view('pages.bets.detail', [
            'bet' => $this->bet(),
            'canCloseBet' => $this->canCloseBet(),
            'organisations' => $organisations->findAll(),
            'optionsByOdds' => $this->optionsByOdds(),
            'optionsByBets' => $this->optionsByBets(),
        ]);
    }
}
