<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

final readonly class RegisterData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $betaKey = null,
    ) {}

    public static function make(string $name, string $email, string $password, ?string $betaKey = null): self
    {
        return new self(name: $name, email: $email, password: $password, betaKey: $betaKey);
    }
}
