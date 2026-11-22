<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('login', [], false));
        }

        $user = $request->user();
        $userRole = is_object($user->role) ? $user->role->value : (string) $user->role;

        if ($userRole !== $role) {
            abort(403, 'Akses tidak diizinkan untuk peran ini.');
        }

        return $next($request);
    }
}
