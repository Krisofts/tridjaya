<?php

namespace App\Auth\Services;

use Illuminate\Http\Request;

class RoutePermissionResolver
{
    /**
     * Convert route name → permission
     */
    public function resolve(Request $request): string
    {
        $route = $request->route()?->getName();

        if (! $route || ! str_contains($route, '.')) {
            return $route ?? '';
        }

        [$resource, $action] = explode('.', $route);

        return $resource . '.' . $this->mapAction($action);
    }

    /**
     * Convention-based mapping (NO CONFIG, NO MATCH PER MODULE)
     */
    private function mapAction(string $action): string
    {
        return match ($action) {

            // read
            'index', 'show'   => 'view',

            // create
            'store'           => 'create',

            // update
            'update'          => 'update',

            // delete
            'destroy'         => 'delete',

            // fallback
            default           => $action,
        };
    }
}