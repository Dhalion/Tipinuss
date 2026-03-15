<?php

namespace App\Livewire\Page\Bets;

use App\Enums\BetStatus;
use App\Models\Bet;
use App\Models\BetOption;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public const int DEFAULT_OPTIONS_COUNT = 2;

    #[Validate('required|min:5|max:255')]
    public string $title = '';

    #[Validate('nullable|max:1000')]
    public string $description = '';

    #[Validate('nullable|date_format:Y-m-d\TH:i')]
    public string $expires_at = '';

    /** @var array<int, array<string, mixed>> */
    public array $options = [];

    public int $optionCount = self::DEFAULT_OPTIONS_COUNT;

    public function mount(): void
    {
        for ($i = 0; $i < self::DEFAULT_OPTIONS_COUNT; $i++) {
            $this->options[] = [
                "title" => "",
                "odds" => $this->calculateBaseOdds($i + 1),
            ];
        }
    }

    public function addOption(): void
    {
        $this->options[] = [
            "title" => "",
            "odds" => $this->calculateBaseOdds($this->optionCount + 1),
        ];
        $this->optionCount++;

        $this->recalculateOdds();
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) > self::DEFAULT_OPTIONS_COUNT) {
            array_splice($this->options, $index, 1);
            $this->optionCount--;

            $this->recalculateOdds();
        }
    }

    public function createBet(): void
    {
        $this->validate([
            'options' => 'required|array|min:' . self::DEFAULT_OPTIONS_COUNT,
            'options.*.title' => 'required|min:1|max:255',
            'options.*.odds' => 'required|numeric|min:1',
        ]);

        $bet = Bet::create([
            'title' => $this->title,
            'description' => $this->description,
            'user_id' => auth()->id(),
            'status' => BetStatus::Open,
            'expires_at' => $this->expires_at,
        ]);

        foreach ($this->options as $optionData) {
            BetOption::create([
                'bet_id' => $bet->id,
                'title' => $optionData['title'],
                'odds' => (float) $optionData['odds'],
                'base_odds' => (float) $optionData['odds'],
            ]);
        }

        session()->flash('success', 'Bet created successfully!');
        $this->redirect(route('bet.detail', $bet));
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

    public function render()
    {
        return view('pages.bets.create');
    }
}
