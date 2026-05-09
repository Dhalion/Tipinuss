<?php

declare(strict_types=1);

namespace App\Console\Commands\Bet;

use App\Enums\BetStatus;
use App\Models\Bet;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class ListBetsCommand extends Command
{
    protected $signature = 'tipinuss:bet:list {--status= : Filter by status: open, closed}';

    protected $description = 'List all bets with their status, creator and option count';

    public function handle(): int
    {
        $query = Bet::with(['creator', 'betOptions'])
            ->withCount('userBets')
            ->latest();

        $statusFilter = $this->option('status');

        if ($statusFilter !== null) {
            $status = BetStatus::tryFrom($statusFilter);

            if ($status === null) {
                $this->error("Invalid status '{$statusFilter}'. Use: open, closed");

                return self::FAILURE;
            }

            $query->where('status', $status->value);
        }

        $bets = $query->get();

        if ($bets->isEmpty()) {
            $this->info('No bets found.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Title', 'Creator', 'Status', 'Options', 'Bets placed', 'Created'],
            $bets->map(fn (Bet $bet) => [
                Str::substr($bet->id, 0, 8).'...',
                Str::limit($bet->title, 40),
                $bet->creator?->email ?? '—',
                $bet->status->value,
                $bet->betOptions->count(),
                $bet->user_bets_count,
                $bet->created_at?->format('Y-m-d H:i'),
            ]),
        );

        return self::SUCCESS;
    }
}
