<?php declare(strict_types=1);

namespace App\Enums;

enum BetStatus: string
{
    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('bets.open'),
            self::Closed => __('bets.closed'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'green',
            self::Closed => 'red',
        };
    }
}
