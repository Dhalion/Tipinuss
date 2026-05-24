<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class BetaGuardMiddleware
{
    private const EXCEPT_ROUTE_NAMES = [
        'login',
        'login.store',
        'register',
        'logout',
        'pending.approval',
        'dusk.login',
        'dusk.logout',
        'dusk.user',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! (bool) config('app.beta_mode', false)) {
            return $next($request);
        }

        $route = $request->route();

        if ($route !== null && $route->named(self::EXCEPT_ROUTE_NAMES)) {
            return $next($request);
        }

        if ($request->is('livewire*')) {
            return $next($request);
        }

        if (! Auth::check()) {
            return redirect()->guest(route('login'));
        }

        /** @var User $user */
        $user = Auth::user();

        if (! $user->isApproved()) {
            return redirect()->route('pending.approval');
        }

        return $next($request);
    }
}
