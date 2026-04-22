<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Bet;
use App\Models\UserBet;
use App\Policies\BetPolicy;
use App\Policies\UserBetPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Bet::class => BetPolicy::class,
        UserBet::class => UserBetPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
