<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Auth\Services\AuthService;

class GroupMiddleware
{
    public function __construct(
        protected AuthService $auth
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$groups
    ): Response {

        if (! $this->auth->check()) {
            abort(401);
        }

        if (! $this->auth->inGroup(...$groups)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}