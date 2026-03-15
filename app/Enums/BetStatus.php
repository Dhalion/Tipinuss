<?php

namespace App\Enums;

enum BetStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'tipinuss-green',
            self::Closed => 'tipinuss-red',
        };
    }
}
