<?php

declare(strict_types=1);

namespace App\Enums;

enum BetaAccessKeyStatus: string
{
    case Available = 'available';
    case Used = 'used';
    case Inactive = 'inactive';

    public function badgeColor(): string
    {
        return match ($this) {
            self::Available => 'green',
            self::Used => 'red',
            self::Inactive => 'yellow',
        };
    }
}
