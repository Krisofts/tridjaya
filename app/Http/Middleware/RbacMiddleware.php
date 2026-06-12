<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Auth\Services\AuthService;

class RbacMiddleware
{
    public function __construct(
        protected AuthService $auth
    ) {}

    public function handle(
        Request $request,
        Closure $next,
        string $rules
    ): Response {

        /*
        |--------------------------------
        | AUTH CHECK
        |--------------------------------
        */
        if (! $this->auth->check()) {
            abort(401);
        }

        /*
        |--------------------------------
        | PARSE RULES
        |--------------------------------
        */
        $rules = array_filter(
            array_map('trim', explode('|', $rules))
        );

        /*
        |--------------------------------
        | EVALUATE RULES (OR LOGIC)
        |--------------------------------
        */
        foreach ($rules as $rule) {

            // GROUP RULE
            if (str_starts_with($rule, 'group:')) {
                $group = substr($rule, 6);

                if ($this->auth->inGroup($group)) {
                    return $next($request);
                }
            }

            // PERMISSION RULE
            if (str_starts_with($rule, 'perm:')) {
                $perm = substr($rule, 5);

                if ($this->auth->canAccess($perm)) {
                    return $next($request);
                }
            }
        }

        /*
        |--------------------------------
        | DENY
        |--------------------------------
        */
        abort(403, 'Unauthorized');
    }
}