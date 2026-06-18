<?php

namespace App\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Auth\Services\AuthorizationService;

class CanAccess
{
    public function __construct(
        protected AuthorizationService $authz
    ) {}

    /**
     * Handle incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if (!$this->authz->canAccess($user, $permission)) {
            abort(403, 'You do not have permission.');
        }

        return $next($request);
    }
}