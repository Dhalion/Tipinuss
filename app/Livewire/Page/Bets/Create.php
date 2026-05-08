<?php

declare(strict_types=1);

namespace App\Livewire\Page\Bets;

use App\Actions\Betting\CreateBetAction;
use App\DTOs\Betting\BetOptionData;
use App\DTOs\Betting\CreateBetData;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class Create extends Component
{
    public const int DEFAULT_OPTIONS_COUNT = 2;

    #[Validate('required|min:5|max:255')]
    public string $title = '';

    #[Validate('nullable|max:1000')]
    public string $description = '';

    #[Validate('nullable|date')]
    public string $expires_at = '';

    /** @var array<int, array<string, mixed>> */
    public array $options = [];

    public int $optionCount = self::DEFAULT_OPTIONS_COUNT;

    public function mount(): void
    {
        for ($i = 0; $i < self::DEFAULT_OPTIONS_COUNT; $i++) {
            $this->options[] = [
                'title' => '',
                'odds' => $this->calculateBaseOdds($i + 1),
            ];
        }
    }

    public function addOption(): void
    {
        $this->options[] = [
            'title' => '',
            'odds' => $this->calculateBaseOdds($this->optionCount + 1),
        ];
        $this->optionCount++;
        $this->recalculateOdds();
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) <= self::DEFAULT_OPTIONS_COUNT) {
            return;
        }

        array_splice($this->options, $index, 1);
        $this->optionCount--;
        $this->recalculateOdds();
    }

    public function createBet(CreateBetAction $action): void
    {
        $this->validate([
            'title' => 'required|min:5|max:255',
            'description' => 'nullable|max:1000',
            'expires_at' => 'nullable|date',
            'options' => 'required|array|min:'.self::DEFAULT_OPTIONS_COUNT,
            'options.*.title' => 'required|min:1|max:255',
            'options.*.odds' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        if ($user === null) {
            return;
        }

        $bet = $action->execute($this->buildCreateBetData($user));

        session()->flash('success', '✓ Wette erfolgreich erstellt!');
        $this->redirect(route('bets.detail', $bet));
    }

    private function buildCreateBetData(User $user): CreateBetData
    {
        return CreateBetData::make(
            creator: $user,
            title: $this->title,
            description: $this->description !== '' ? $this->description : null,
            expiresAt: $this->parseExpiresAt(),
            options: $this->buildOptionDTOs(),
            organisationId: $user->organisation_id,
        );
    }

    private function parseExpiresAt(): ?CarbonImmutable
    {
        if ($this->expires_at === '') {
            return null;
        }

        return CarbonImmutable::parse($this->expires_at);
    }

    /** @return array<int, BetOptionData> */
    private function buildOptionDTOs(): array
    {
        return array_map(
            fn (array $option): BetOptionData => BetOptionData::make(
                title: (string) $option['title'],
                odds: (float) $option['odds'],
            ),
            $this->options,
        );
    }

    private function calculateBaseOdds(int $optionCount): float
    {
        return round(1 + (0.5 * ($optionCount - 1)), 2);
    }

    private function recalculateOdds(): void
    {
        $newOdds = $this->calculateBaseOdds(count($this->options));

        foreach ($this->options as &$option) {
            $option['odds'] = $newOdds;
        }
    }

    public function render(): View
    {
        return view('pages.bets.create');
    }
}
