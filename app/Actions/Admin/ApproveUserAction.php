<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

final class ApproveUserAction
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function execute(User $user, ?string $organisationId = null): User
    {
        $user->is_approved = true;

        if ($organisationId !== null && $organisationId !== '') {
            $user->organisation_id = $organisationId;
        }

        return $this->users->save($user);
    }
}
