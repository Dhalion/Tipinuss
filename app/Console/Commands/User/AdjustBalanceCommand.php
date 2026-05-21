<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Actions\Admin\AdjustUserBalanceAction;
use App\Models\User;
use Illuminate\Console\Command;

final class AdjustBalanceCommand extends Command
{
    protected $signature = 'tipinuss:user:adjust-balance {email : User email} {--amount= : Signed integer amount (e.g. 100 or -50)}';

    protected $description = 'Adjust a user\'s Soapnut balance by a signed amount';

    public function __construct(private AdjustUserBalanceAction $adjustBalance)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error("No user found with email '{$this->argument('email')}'.");

            return self::FAILURE;
        }

        $amount = (int) $this->option('amount');

        if ($this->option('amount') === null) {
            $this->error('Please provide an amount with --amount=<value> (e.g. --amount=100 or --amount=-50).');

            return self::FAILURE;
        }

        $balanceBefore = $user->soapnuts;

        $this->adjustBalance->execute($user, $amount);

        $user->refresh();
        $balanceAfter = $user->soapnuts;

        $sign = $amount >= 0 ? '+' : '';
        $this->info("✓ {$user->email}: {$balanceBefore} 🌰 → {$balanceAfter} 🌰 ({$sign}{$amount})");

        return self::SUCCESS;
    }
}
