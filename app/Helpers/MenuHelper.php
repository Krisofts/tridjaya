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
                'groups' => ['superadmin', 'manager', 'finance'],
                'subItems' => [
                    ['name' => 'Penjualan', 'route' => 'dashboard.sales.index'],
                ],
            ],
            [
                'icon' => 'ecommerce',
                'name' => 'Ecommerce',
                'groups' => ['superadmin', 'manager', 'finance'],
                'subItems' => [
                    ['name' => 'Dashboard', 'route' => 'ecommerce.dashboard'],
                    ['name' => 'Produk',    'route' => 'ecommerce.products.index'],
                    ['name' => 'Kategori',  'route' => 'ecommerce.categories.index'],
                    ['name' => 'Order',     'route' => 'ecommerce.orders.index'],
                    ['name' => 'Customer',  'route' => 'ecommerce.customers.index'],
                    ['name' => 'Laporan',   'route' => 'ecommerce.reports.index'],
                ],
            ],
            [
                'icon' => 'user-profile',
                'name' => 'Sales',
                'groups' => ['superadmin', 'manager'],
                'subItems' => [
                    ['name' => 'Performance', 'route' => 'sales.performance'],
                    ['name' => 'Analytics',   'route' => 'sales.analytics'],
                ],
            ],
            [
                'icon' => 'user-profile',
                'name' => 'HR',
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
                'groups' => ['superadmin', 'owner', 'manager', 'sales'],
                'subItems' => [
                    ['name' => 'Dashboard',    'route' => 'crm.dashboard'],
                    ['name' => 'Semua Lead',   'route' => 'crm.leads.index'],
                    ['name' => 'Tasks',        'route' => 'crm.tasks.index'],
                    ['name' => 'Laporan Lead',       'route' => 'crm.reports.leads'],
                    ['name' => 'Performa Sales',     'route' => 'crm.reports.sales-performance'],
                    ['name' => 'Laporan Aktivitas',  'route' => 'crm.reports.activities'],
                    ['name' => 'Notifikasi',   'route' => 'crm.notifications.index'],
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

    public static function getIconSvg(string $icon): string
    {
        $icons = [
            'dashboard'    => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
            'ecommerce'    => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
            'user-profile' => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
            'crm'          => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'default'      => '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>',
        ];

        return $icons[$icon] ?? $icons['default'];
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
