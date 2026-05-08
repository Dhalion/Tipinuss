<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    public function all(): Collection
    {
        return User::orderBy('name')->get();
    }

    public function allWithBetCount(): Collection
    {
        return User::withCount('userBets')
            ->with('organisation')
            ->orderBy('name')
            ->get();
    }

    public function save(User $user): User
    {
        $user->save();

        return $user;
    }

    public function topBySoapnuts(int $limit = 10): Collection
    {
        return User::withCount('userBets')
            ->orderByDesc('soapnuts')
            ->take($limit)
            ->get();
    }
}
