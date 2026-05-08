<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function topBySoapnuts(int $limit = 10): Collection
    {
        return User::withCount('userBets')
            ->orderByDesc('soapnuts')
            ->take($limit)
            ->get();
    }
}
