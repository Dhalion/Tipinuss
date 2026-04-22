<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserBet;

class UserBetPolicy
{
    public function viewUserBet(User $user, UserBet $userBet): bool
    {
        return true;
    }
}
