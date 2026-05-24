<?php

declare(strict_types=1);

namespace App\Console\Commands\Organisation;

use App\Models\Organisation;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class ListOrganisationsCommand extends Command
{
    protected $signature = 'tipinuss:organisation:list';

    protected $description = 'List all organisations with their member and bet counts';

    public function __construct(private OrganisationRepositoryInterface $organisations)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $orgs = $this->organisations->findAll();

        if ($orgs->isEmpty()) {
            $this->info('No organisations found.');

            return self::SUCCESS;
        }

        $orgs->loadMissing('users', 'bets');

        $this->table(
            ['Name', 'Members', 'Bets', 'ID'],
            $orgs->map(fn (Organisation $org) => [
                $org->name,
                $org->users->count(),
                $org->bets->count(),
                Str::substr($org->id, 0, 8).'...',
            ]),
        );

        return self::SUCCESS;
    }
}
