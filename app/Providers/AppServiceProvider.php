<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentBetOptionRepository;
use App\Repositories\Eloquent\EloquentBetRepository;
use App\Repositories\Eloquent\EloquentUserBetRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BetRepositoryInterface::class, EloquentBetRepository::class);
        $this->app->bind(BetOptionRepositoryInterface::class, EloquentBetOptionRepository::class);
        $this->app->bind(UserBetRepositoryInterface::class, EloquentUserBetRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }

    public function boot(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(app()->isProduction());

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
            : null,
        );
    }
}
