<?php

namespace App\CRM\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Centralized cache key management untuk CRM.
 *
 * Semua cache key CRM ada di sini agar mudah di-invalidate
 * tanpa perlu cari-cari di banyak service.
 *
 * TTL defaults:
 *   - Master data (pipeline, source, interest): 24 jam — berubah jarang
 *   - Dashboard stats: 5 menit — cukup fresh untuk operasional
 *   - Notification count: 30 detik — harus near-realtime
 *   - User-specific data: 2 menit
 */
class CrmCacheService
{
    // -------------------------------------------------------------------------
    // TTL CONSTANTS
    // -------------------------------------------------------------------------
    public const TTL_MASTER  = 86400;  // 24 jam — master data
    public const TTL_STATS   = 300;    // 5 menit — dashboard stats
    public const TTL_NOTIF   = 30;     // 30 detik — notification count
    public const TTL_USER    = 120;    // 2 menit — user-specific

    // -------------------------------------------------------------------------
    // KEY BUILDERS
    // -------------------------------------------------------------------------

    public static function keyMasterPipelines(): string
    {
        return 'crm:master:pipelines';
    }

    public static function keyMasterSources(): string
    {
        return 'crm:master:sources';
    }

    public static function keyMasterInterests(): string
    {
        return 'crm:master:interests';
    }

    public static function keyMasterLostReasons(?int $pipelineId): string
    {
        return 'crm:master:lost-reasons:' . ($pipelineId ?? 'global');
    }

    public static function keyMasterActivityTypes(): string
    {
        return 'crm:master:activity-types';
    }

    public static function keyMasterActivityResults(int $typeId): string
    {
        return 'crm:master:activity-results:' . $typeId;
    }

    public static function keyDashboardManagerStats(?int $branchId): string
    {
        return 'crm:dashboard:manager-stats:' . ($branchId ?? 'all');
    }

    public static function keyDashboardPipeline(?int $branchId): string
    {
        return 'crm:dashboard:pipeline:' . ($branchId ?? 'all');
    }

    public static function keyDashboardTrend(?int $branchId): string
    {
        return 'crm:dashboard:trend:' . ($branchId ?? 'all');
    }

    public static function keyDashboardSalesStats(int $userId): string
    {
        return 'crm:dashboard:sales-stats:' . $userId;
    }

    public static function keyNotifCount(int $userId): string
    {
        return 'crm:notif:count:' . $userId;
    }

    // -------------------------------------------------------------------------
    // REMEMBER HELPERS
    // -------------------------------------------------------------------------

    public static function rememberMaster(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, self::TTL_MASTER, $callback);
    }

    public static function rememberStats(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, self::TTL_STATS, $callback);
    }

    public static function rememberUser(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, self::TTL_USER, $callback);
    }

    public static function rememberNotif(string $key, \Closure $callback): mixed
    {
        return Cache::remember($key, self::TTL_NOTIF, $callback);
    }

    // -------------------------------------------------------------------------
    // INVALIDATION
    // -------------------------------------------------------------------------

    /**
     * Hapus semua cache master data.
     * Dipanggil saat ada perubahan di master data (pipeline, source, dll).
     */
    public static function flushMaster(): void
    {
        Cache::forget(self::keyMasterPipelines());
        Cache::forget(self::keyMasterSources());
        Cache::forget(self::keyMasterInterests());
        Cache::forget(self::keyMasterActivityTypes());
    }

    /**
     * Hapus cache dashboard.
     * Dipanggil saat ada lead baru / perubahan status lead.
     */
    public static function flushDashboard(?int $branchId = null): void
    {
        Cache::forget(self::keyDashboardManagerStats($branchId));
        Cache::forget(self::keyDashboardPipeline($branchId));
        Cache::forget(self::keyDashboardTrend($branchId));

        // Juga flush semua branch jika branchId null (superadmin)
        Cache::forget(self::keyDashboardManagerStats(null));
        Cache::forget(self::keyDashboardPipeline(null));
        Cache::forget(self::keyDashboardTrend(null));
    }

    /**
     * Hapus cache stats sales tertentu.
     */
    public static function flushSalesStats(int $userId): void
    {
        Cache::forget(self::keyDashboardSalesStats($userId));
    }

    /**
     * Hapus cache notifikasi user.
     */
    public static function flushNotifCount(int $userId): void
    {
        Cache::forget(self::keyNotifCount($userId));
    }

    /**
     * Hapus semua cache CRM — untuk emergency / deploy.
     */
    public static function flushAll(): void
    {
        // Gunakan tag jika Redis mendukung, fallback ke pattern
        if (config('cache.default') === 'redis') {
            // Redis: delete by prefix pattern
            $redis = Cache::getStore()->getRedis();
            $prefix = config('cache.prefix') . ':crm:*';
            $keys   = $redis->keys($prefix);
            if (! empty($keys)) {
                $redis->del($keys);
            }
        } else {
            // Fallback: flush per key yang diketahui
            self::flushMaster();
            self::flushDashboard();
        }
    }
}