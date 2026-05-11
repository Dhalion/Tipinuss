<?php

declare(strict_types=1);

namespace App\Enums;

enum UserBetStatus: string
{
    case Pending = 'pending';
    case Won = 'won';
    case Lost = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('bets.pending'),
            self::Won => __('bets.won'),
            self::Lost => __('bets.lost'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Won => 'green',
            self::Lost => 'red',
        };
    }
}
