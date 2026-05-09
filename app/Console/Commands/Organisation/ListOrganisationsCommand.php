<?php

declare(strict_types=1);

namespace App\Console\Commands\Organisation;

use App\Models\Organisation;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class ListOrganisationsCommand extends Command
{
    protected $signature = 'tipinuss:organisation:list';

    protected $description = 'List all organisations with their member and bet counts';

    public function handle(): int
    {
        $organisations = Organisation::withCount(['users', 'bets'])->orderBy('name')->get();

        if ($organisations->isEmpty()) {
            $this->info('No organisations found.');

            return self::SUCCESS;
        }

        $this->table(
            ['Name', 'Members', 'Bets', 'ID'],
            $organisations->map(fn (Organisation $org) => [
                $org->name,
                $org->users_count,
                $org->bets_count,
                Str::substr($org->id, 0, 8).'...',
            ]),
        );

        return self::SUCCESS;
    }
}
