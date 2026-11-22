<?php

use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->redirectUsersTo(function (Request $request): string {
            $user = $request->user();

            if ($user !== null && $user->isSeller()) {
                return route('seller.dashboard');
            }

            return route('buyer.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
