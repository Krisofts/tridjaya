<?php

namespace App\CRM\Observers;

use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmSource;
use App\CRM\Models\CrmInterest;
use App\CRM\Models\CrmActivityType;
use App\CRM\Models\CrmActivityResult;
use App\CRM\Services\CrmCacheService;

/**
 * Observer untuk auto-invalidate cache master data
 * saat ada perubahan di master data.
 *
 * Daftarkan di AppServiceProvider::boot():
 *
 *   CrmPipeline::observe(CacheInvalidationObserver::class);
 *   CrmSource::observe(CacheInvalidationObserver::class);
 *   CrmInterest::observe(CacheInvalidationObserver::class);
 *   CrmActivityType::observe(CacheInvalidationObserver::class);
 *   CrmActivityResult::observe(CacheInvalidationObserver::class);
 */
class CacheInvalidationObserver
{
    public function saved($model): void
    {
        $this->flush($model);
    }

    public function deleted($model): void
    {
        $this->flush($model);
    }

    private function flush($model): void
    {
        match (true) {
            $model instanceof CrmPipeline       => CrmCacheService::flushMaster(),
            $model instanceof CrmSource         => \Illuminate\Support\Facades\Cache::forget(CrmCacheService::keyMasterSources()),
            $model instanceof CrmInterest       => \Illuminate\Support\Facades\Cache::forget(CrmCacheService::keyMasterInterests()),
            $model instanceof CrmActivityType   => \Illuminate\Support\Facades\Cache::forget(CrmCacheService::keyMasterActivityTypes()),
            $model instanceof CrmActivityResult => \Illuminate\Support\Facades\Cache::forget(
                CrmCacheService::keyMasterActivityResults($model->activity_type_id)
            ),
            default => null,
        };
    }
}