<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\Betting\BetClosed;
use App\Listeners\Betting\NotifyBetParticipants;
use App\Models\User;
use App\Repositories\Contracts\BalanceTransactionRepositoryInterface;
use App\Repositories\Contracts\BetaAccessKeyRepositoryInterface;
use App\Repositories\Contracts\BetOptionRepositoryInterface;
use App\Repositories\Contracts\BetRepositoryInterface;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use App\Repositories\Contracts\UserBetRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentBalanceTransactionRepository;
use App\Repositories\Eloquent\EloquentBetaAccessKeyRepository;
use App\Repositories\Eloquent\EloquentBetOptionRepository;
use App\Repositories\Eloquent\EloquentBetRepository;
use App\Repositories\Eloquent\EloquentOrganisationRepository;
use App\Repositories\Eloquent\EloquentUserBetRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Dusk\DuskServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (! $this->app->environment('production')) {
            $this->app->register(DuskServiceProvider::class);
        }

        $this->app->bind(BalanceTransactionRepositoryInterface::class, EloquentBalanceTransactionRepository::class);
        $this->app->bind(BetaAccessKeyRepositoryInterface::class, EloquentBetaAccessKeyRepository::class);
        $this->app->bind(BetRepositoryInterface::class, EloquentBetRepository::class);
        $this->app->bind(BetOptionRepositoryInterface::class, EloquentBetOptionRepository::class);
        $this->app->bind(UserBetRepositoryInterface::class, EloquentUserBetRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(OrganisationRepositoryInterface::class, EloquentOrganisationRepository::class);
    }

    public function boot(): void
    {
        Date::use(CarbonImmutable::class);

        Model::preventLazyLoading(! app()->isProduction());
        Model::shouldBeStrict(! app()->isProduction());

        DB::prohibitDestructiveCommands(app()->isProduction());

        View::share('showBetaBadge', (bool) config('app.beta_mode', false));

        Blade::directive('appVersion', fn (): string => "<?php echo \App\Services\VersionService::label(); ?>");

        Gate::define('admin', fn (User $user): bool => $user->isAdmin());

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)->mixedCase()->letters()->numbers()->symbols()->uncompromised()
            : null,
        );

        Event::listen(BetClosed::class, NotifyBetParticipants::class);
    }
}
