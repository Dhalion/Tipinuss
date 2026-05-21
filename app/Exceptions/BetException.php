<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class BetException extends Exception
{
    public static function withMessage(string $message): self
    {
        return new self($message);
    }

    public static function optionNotFound(): self
    {
        return new self(__('bets.option_not_found'));
    }

    public static function betAlreadyClosed(): self
    {
        return new self(__('bets.bet_closed_error'));
    }

    public static function insufficientBalance(int $shortfall): self
    {
        return new self(__('bets.insufficient_balance', ['shortfall' => $shortfall]));
    }

    public static function amountOutOfBounds(int $min, int $max): self
    {
        return new self(__('bets.amount_out_of_bounds', ['min' => $min, 'max' => $max]));
    }

    public static function wrongBetOption(): self
    {
        return new self(__('bets.wrong_bet_option'));
    }

    public static function cannotDeleteSelf(): self
    {
        return new self(__('admin.users.cannot_delete_self'));
    }

    public static function cannotToggleSelf(): self
    {
        return new self(__('admin.users.cannot_toggle_self'));
    }

    public static function balanceDecrementFailed(): self
    {
        return new self(__('account.balance_decrement_failed'));
    }
}
