<?php

namespace App\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Auth\Services\AuthorizationService;
use App\Auth\Services\RoutePermissionResolver;

class PermissionMiddleware
{
    public function __construct(
        protected AuthorizationService $authz,
        protected RoutePermissionResolver $resolver
    ) {}

    /**
     * Handle request
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // 1. must login
        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        // 2. resolve route → permission
        $permission = $this->resolver->resolve($request);

        // 3. guard permission
        if (! $this->authz->hasPermission($user, $permission)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}