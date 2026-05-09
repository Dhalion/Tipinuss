<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Models\Organisation;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Console\Command;

final class AssignOrganisationCommand extends Command
{
    protected $signature = 'tipinuss:user:assign-org {email : User email} {org : Organisation UUID or name}';

    protected $description = 'Assign a user to an organisation';

    public function __construct(private UserRepositoryInterface $users)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        $orgIdentifier = $this->argument('org');
        $organisation = Organisation::where('id', $orgIdentifier)
            ->orWhere('name', $orgIdentifier)
            ->first();

        if ($organisation === null) {
            $this->error("No organisation found matching '{$orgIdentifier}'.");

            return self::FAILURE;
        }

        $user->organisation_id = $organisation->id;
        $this->users->save($user);

        $this->info("✓ {$user->email} assigned to organisation '{$organisation->name}'.");

        return self::SUCCESS;
    }
}
