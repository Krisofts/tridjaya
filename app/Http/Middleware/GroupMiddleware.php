<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$groups
    ): Response {

        $user = auth()->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->inGroup(...$groups)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}