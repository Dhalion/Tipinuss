<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

final class RegisterData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {}

    public static function make(string $name, string $email, string $password): self
    {
        return new self(name: $name, email: $email, password: $password);
    }
}
