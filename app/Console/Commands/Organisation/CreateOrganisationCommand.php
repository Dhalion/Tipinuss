<?php

declare(strict_types=1);

namespace App\Console\Commands\Organisation;

use App\Models\Organisation;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use Illuminate\Console\Command;

final class CreateOrganisationCommand extends Command
{
    protected $signature = 'tipinuss:organisation:create {name : Name of the new organisation}';

    protected $description = 'Create a new organisation';

    public function __construct(private OrganisationRepositoryInterface $organisations)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $name = $this->argument('name');

        if (Organisation::where('name', $name)->exists()) {
            $this->error("An organisation named '{$name}' already exists.");

            return self::FAILURE;
        }

        $organisation = new Organisation;
        $organisation->name = $name;
        $this->organisations->save($organisation);

        $this->info("✓ Organisation '{$organisation->name}' created (ID: {$organisation->id}).");

        return self::SUCCESS;
    }
}
