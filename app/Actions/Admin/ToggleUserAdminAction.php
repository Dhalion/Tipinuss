<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Exceptions\BetException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

final class ToggleUserAdminAction
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function execute(User $admin, User $target): void
    {
        if ($admin->id === $target->id) {
            throw new BetException('Admins cannot change their own admin status.');
        }

        $target->is_admin = ! $target->is_admin;
        $this->users->save($target);
    }
}
