<?php

declare(strict_types=1);

namespace App\Console\Commands\Bet;

use App\Actions\Betting\DeleteBetAction;
use App\DTOs\Betting\DeleteBetData;
use App\Repositories\Contracts\BetRepositoryInterface;
use Illuminate\Console\Command;

final class DeleteBetCommand extends Command
{
    protected $signature = 'tipinuss:bet:delete {bet : Bet UUID}';

    protected $description = 'Permanently delete a bet and all associated data';

    public function __construct(
        private DeleteBetAction $deleteBet,
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

        $this->line("Title:   {$bet->title}");
        $this->line("Status:  {$bet->status->value}");
        $this->line("Created: {$bet->created_at?->format('Y-m-d H:i')}");

        if (! $this->confirm('Permanently delete this bet? This cannot be undone.')) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $this->deleteBet->execute(new DeleteBetData(bet: $bet));

        $this->info("✓ Bet '{$bet->title}' deleted.");

        return self::SUCCESS;
    }
}
