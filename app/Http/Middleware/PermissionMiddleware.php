<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$permissions
    ): Response {

        $user = auth()->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->can(...$permissions)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}