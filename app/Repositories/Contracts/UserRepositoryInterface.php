<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /** @return Collection<int, User> */
    public function topBySoapnuts(int $limit = 10): Collection;
}
