<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Exceptions\BetException;
use App\Models\User;

final class ToggleUserAdminAction
{
    public function execute(User $admin, User $target): void
    {
        if ($admin->id === $target->id) {
            throw new BetException('Admins cannot change their own admin status.');
        }

        $target->update(['is_admin' => ! $target->is_admin]);
    }
}
