<?php

declare(strict_types=1);

namespace App\Console\Commands\Bet;

use App\Actions\Betting\CloseBetAction;
use App\DTOs\Betting\CloseBetData;
use App\Models\Bet;
use App\Models\BetOption;
use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Console\Command;

final class CloseBetCommand extends Command
{
    protected $signature = 'tipinuss:bet:close {bet : Bet UUID} {option? : Winning option UUID}';

    protected $description = 'Close a bet and pay out winnings to the winning option holders';

    public function __construct(
        private CloseBetAction $closeBet,
        private BetRepositoryInterface $bets,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $bet = $this->bets->findById($this->argument('bet'));

        if ($bet === null) {
            $this->error('Bet not found.');

            return self::FAILURE;
        }

        $bet->load('betOptions');

        if ($bet === null) {
            $this->error('Bet not found.');

            return self::FAILURE;
        }

        if (! $bet->isOpen()) {
            $this->error("Bet '{$bet->title}' is already closed.");

            return self::FAILURE;
        }

        $winningOptionId = $this->resolveWinningOptionId($bet);

        if ($winningOptionId === null) {
            return self::FAILURE;
        }

        if (! $this->confirm("Close bet '{$bet->title}'? This will pay out winnings and cannot be undone.")) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $this->closeBet->execute(new CloseBetData(bet: $bet, winningOptionId: $winningOptionId));

        $this->info("✓ Bet '{$bet->title}' closed. Winnings paid out.");

        return self::SUCCESS;
    }

    private function resolveWinningOptionId(Bet $bet): ?string
    {
        $optionArgument = $this->argument('option');

        if ($optionArgument !== null) {
            return $optionArgument;
        }

        $options = $bet->betOptions;

        if ($options->isEmpty()) {
            $this->error('This bet has no options.');

            return null;
        }

        $labelMap = $options->mapWithKeys(
            fn (BetOption $option) => ["{$option->title} (odds: {$option->odds})" => $option->id],
        )->all();

        $selected = $this->choice('Select the winning option:', array_keys($labelMap));

        return $labelMap[$selected] ?? null;
    }
}
