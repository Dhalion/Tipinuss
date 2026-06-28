<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Models\User;

final readonly class RegisterResult
{
    public function __construct(
        public User $user,
        public ?int $startBalance = null,
        public ?string $tokenMessage = null,
    ) {}
}
