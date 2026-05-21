<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionType: string
{
    case Initial = 'initial';
    case BetPlaced = 'bet_placed';
    case BetWon = 'bet_won';
    case AdminAdjustment = 'admin_adjustment';
}
