<?php

declare(strict_types=1);

namespace App\Console\Commands\Organisation;

use App\Repositories\Contracts\OrganisationRepositoryInterface;
use Illuminate\Console\Command;

final class DeleteOrganisationCommand extends Command
{
    protected $signature = 'tipinuss:organisation:delete {id : Organisation UUID}';

    protected $description = 'Delete an organisation (users and bets are unaffected, org reference is nulled)';

    public function __construct(private OrganisationRepositoryInterface $organisations)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $organisation = $this->organisations->findById($this->argument('id'));

        if ($organisation === null) {
            $this->error("No organisation found with ID '{$this->argument('id')}'.");

            return self::FAILURE;
        }

        $this->line("Name:    {$organisation->name}");
        $this->line("ID:      {$organisation->id}");

        if (! $this->confirm('Delete this organisation? Users and bets will have their org reference nulled.')) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $this->organisations->delete($organisation);

        $this->info("✓ Organisation '{$organisation->name}' deleted.");

        return self::SUCCESS;
    }
}
