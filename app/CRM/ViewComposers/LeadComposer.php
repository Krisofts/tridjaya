<?php

namespace App\CRM\ViewComposers;

use App\CRM\Models\CrmInterest;
use App\CRM\Models\CrmLeadSource;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Models\CrmResult;
use App\Models\Branch;
use App\Services\RegionService;
use App\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class LeadComposer
{
    public function __construct(
        protected RegionService $region,
    ) {}

    public function compose(View $view): void
    {
        $users = $this->toOptions(User::query()->orderBy('name')->get());

        $view->with([
            'sources'      => $this->toOptions(CrmLeadSource::active()->orderBy('name')->get()),
            'pipelines'    => $this->toOptions(CrmPipeline::active()->orderBy('name')->get()),
            'interests'    => $this->toOptions(CrmInterest::active()->orderBy('sort_order')->get()),
            'users'        => $users,
            'assignees'    => $users,
            'branches'     => $this->toOptions(Branch::query()->orderBy('name')->get()),
            'temperatures' => $this->temperatureOptions(),

            // Provinces di-load sekali di server — format ['code' => 'name']
            // Cities & districts tidak di-load di sini — diambil via AJAX (RegionController)
            'provinces'    => $this->region->provinces(),

            // Results untuk complete task modal di show blade
            'results'      => CrmResult::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray(),
        ]);
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    /**
     * Convert Eloquent collection ke format ['id' => 'name', ...]
     * yang langsung bisa dipakai komponen select via :options="$var"
     */
    private function toOptions(Collection $collection, string $valueKey = 'id', string $labelKey = 'name'): array
    {
        return $collection
            ->pluck($labelKey, $valueKey)
            ->toArray();
    }

    private function temperatureOptions(): array
    {
        return [
            CrmPipelineStage::TEMP_COLD     => 'Cold',
            CrmPipelineStage::TEMP_WARM     => 'Warm',
            CrmPipelineStage::TEMP_HOT      => 'Hot',
            CrmPipelineStage::TEMP_CUSTOMER => 'Customer',
            CrmPipelineStage::TEMP_LOST     => 'Lost',
        ];
    }
}