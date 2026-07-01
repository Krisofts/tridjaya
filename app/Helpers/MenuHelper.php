<?php

namespace App\Helpers;

use App\Auth\Services\AuthorizationService;

class MenuHelper
{
    public static function getMainNavItems(): array
    {
        $menus = [
            [
                'icon' => 'dashboard',
                'name' => 'Dashboard',
                'groups' => ['superadmin', 'owner' ,'manager', 'finance'],
                'subItems' => [
                    ['name' => 'Penjualan', 'route' => 'dashboard.sales'],
                ],
            ],
          
            [
                'icon' => 'user-profile',
                'name' => 'Sales',
                'groups' => ['superadmin', 'hrd'],
                'subItems' => [
                    ['name' => 'Performance', 'route' => 'sales.performance'],
                    ['name' => 'Analytics',   'route' => 'sales.analytics'],
                ],
            ],
            [
                'icon' => 'hrd',
                'name' => 'HRD GA',
                'groups' => ['superadmin', 'manager'],
                'subItems' => [
                    ['name' => 'Users', 'route' => 'users.index'],
                ],
            ],

            // ----------------------------------------------------------------
            // CRM — Manager/Owner/Superadmin: lihat semua + master data
            // ----------------------------------------------------------------
            [
                'icon'   => 'crm',
                'name'   => 'CRM',
                'groups' => ['superadmin', 'owner', 'manager', 'sales', 'driver','pdi',],
                'subItems' => [
                    ['name' => 'Dashboard',    'route' => 'crm.dashboard'],
                    ['name' => 'Semua Lead',  'groups' => ['superadmin', 'manager'],  'route' => 'crm.leads.index'],
                    ['name' => 'Lead Saya',   'route' => 'crm.leads.my-leads'],
                    ['name' => 'Tasks',        'route' => 'crm.tasks.index'],
                    ['name' => 'Laporan',     'route' => 'crm.reports.index'],
                    ['name' => 'Alasan Lost',  'route' => 'crm.lost-reasons.index'],
                ],
            ],


        ];

        return self::filterMenus($menus);
    }

    public static function getOperationalItems(): array
    {
        $menus = [
            // ----------------------------------------------------------------
            // CRM — Sales: hanya lihat data sendiri
            // ----------------------------------------------------------------

        ];

        return self::filterMenus($menus);
    }

    public static function getMenuGroups(): array
    {
        return [
            ['title' => 'Menu',   'items' => self::getMainNavItems()],
            ['title' => '', 'items' => self::getOperationalItems()],
        ];
    }

    /**
     * Resolve route name ke URL. Return '#' jika route tidak terdaftar.
     */
    public static function url(string $routeName): string
    {
        try {
            return route($routeName);
        } catch (\Throwable) {
            return '#';
        }
    }

    /**
     * Ambil hanya path dari route URL (tanpa domain).
     */
    public static function path(string $routeName): string
    {
        $url = self::url($routeName);
        if ($url === '#') return '#';
        return '/' . ltrim(parse_url($url, PHP_URL_PATH), '/');
    }

    /**
     * Cek apakah route ini aktif.
     * Exact: /crm/leads aktif hanya di /crm/leads
     * Prefix: /crm/leads aktif juga di /crm/leads/1, /crm/leads/create, dst
     */
    public static function isActive(string $routeName, bool $prefix = true): bool
    {
        try {
            $path    = self::path($routeName);
            $current = '/' . ltrim(request()->path(), '/');

            if ($path === '#') return false;

            if ($prefix) {
                return $current === $path
                    || str_starts_with($current, rtrim($path, '/') . '/');
            }

            return $current === $path;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Cek apakah salah satu subItem dalam menu aktif (untuk auto-open parent).
     */
    public static function isMenuActive(array $subItems): bool
    {
        foreach ($subItems as $item) {
            if (self::isActive($item['route'] ?? '')) {
                return true;
            }
        }
        return false;
    }

   
    // =========================================================================
    // Private
    // =========================================================================

    private static function filterMenus(array $menus): array
    {
        $user = auth()->user();

        if (! $user) return [];

        $auth = app(AuthorizationService::class);

        return collect($menus)
            ->filter(function ($menu) use ($auth, $user) {
                if (! isset($menu['groups'])) return true;
                return $auth->inGroup($user, $menu['groups']);
            })
            ->map(function ($menu) use ($auth, $user) {
                $menu['subItems'] = collect($menu['subItems'])
                    ->filter(function ($item) use ($auth, $user) {
                        if (! isset($item['groups'])) return true;
                        return $auth->inGroup($user, $item['groups']);
                    })
                    ->values()
                    ->all();
                return $menu;
            })
            ->filter(fn($menu) => count($menu['subItems']) > 0)
            ->values()
            ->all();
    }
}
