<?php

declare(strict_types=1);

namespace App\Exceptions;

final class InvalidBetaKeyException extends BetException
{
    public static function notFound(string $key): self
    {
        return new self(__('auth.beta_key_invalid', ['key' => $key]));
    }

    public static function alreadyUsed(): self
    {
        return new self(__('auth.beta_key_already_used'));
    }

    public static function expired(): self
    {
        return new self(__('auth.beta_key_expired'));
    }

    public static function inactive(): self
    {
        return new self(__('auth.beta_key_inactive'));
    }
}
