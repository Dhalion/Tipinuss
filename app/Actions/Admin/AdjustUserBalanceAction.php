<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

final class AdjustUserBalanceAction
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function execute(User $target, int $adjustment): void
    {
        $newBalance = max(0, (int) $target->soapnuts + $adjustment);
        $target->soapnuts = $newBalance;
        $this->users->save($target);
    }
}
