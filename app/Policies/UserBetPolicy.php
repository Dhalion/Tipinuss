<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserBet;

final class UserBetPolicy
{
    public function viewUserBet(User $user, UserBet $userBet): bool
    {
        return $user->isAdmin() || $user->id === $userBet->user_id;
    }
}
