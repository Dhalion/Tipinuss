<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Repositories\Contracts\OrganisationRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

final class AssignOrganisationCommand extends Command
{
    protected $signature = 'tipinuss:user:assign-org {email : User email} {org : Organisation UUID or name}';

    protected $description = 'Assign a user to an organisation';

    public function __construct(
        private UserRepositoryInterface $users,
        private OrganisationRepositoryInterface $organisations,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $user = $this->users->findByEmail($this->argument('email'));

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        $orgIdentifier = $this->argument('org');
        $organisation = $this->organisations->findById($orgIdentifier);

        if ($organisation === null) {
            $organisations = $this->organisations->findAll();
            $organisation = $organisations->firstWhere('name', $orgIdentifier);
        }

        if ($organisation === null) {
            $this->error("No organisation found matching '{$orgIdentifier}'.");

            return self::FAILURE;
        }

        $user->organisation_id = $organisation->id;
        $this->users->save($user);

        $this->info("{$user->email} assigned to organisation '{$organisation->name}'.");

        return self::SUCCESS;
    }
}
