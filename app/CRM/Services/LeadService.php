<?php

namespace App\CRM\Services;

use App\CRM\Filters\LeadFilter;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmLeadSource;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Events\LeadCreated;
use App\CRM\Events\LeadStageChanged;
use App\Models\Branch;
use App\Services\RegionService;
use App\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeadService
{
    public function __construct(
        protected LeadFilter $filter,
        protected RegionService $region
    ) {}

    /*
    |--------------------------------------------------------------------------
    | PAGINATION + FILTER
    |--------------------------------------------------------------------------
    */
    public function getPaginated(
        ?string $search = null,
        ?int $sourceId = null,
        ?int $pipelineId = null,
        ?string $temperature = null,
        ?int $assignedTo = null,
        int $perPage = 15
    ): LengthAwarePaginator {

        $query = CrmLead::query()->with([
            'source',
            'pipeline',
            'stage',
            'assignee',
            'creator',
            'branch',
        ]);

        $query = $this->filter->apply($query, [
            'search' => $search,
            'source_id' => $sourceId,
            'pipeline_id' => $pipelineId,
            'temperature' => $temperature,
            'assigned_to' => $assignedTo, // 👤 ADD THIS
        ]);

        return $query->latest()
        ->paginate($perPage)
        ->withQueryString();
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL
    |--------------------------------------------------------------------------
    */
    public function find(int $id): CrmLead
    {
        return CrmLead::query()
        ->with([
            'source',
            'pipeline',
            'stage',
            'activities.user',
            'assignee',
            'creator',
            'branch',
        ])
        ->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data): CrmLead
    {
        $data['lead_code'] = $this->generateLeadCode();

        $firstStage = CrmPipelineStage::query()
        ->where('pipeline_id', $data['pipeline_id'])
        ->orderBy('sort_order')
        ->orderBy('id')
        ->first();

        if (! $firstStage) {
            throw new \Exception('No pipeline stage found for pipeline ID: ' . $data['pipeline_id']);
        }

        $data['pipeline_stage_id'] = $firstStage->id;

        $lead = CrmLead::create($data);

        event(new LeadCreated($lead));

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(CrmLead $lead, array $data): CrmLead
    {
        $stageChanged = false;

        if (
            isset($data['pipeline_id']) &&
            $data['pipeline_id'] != $lead->pipeline_id
        ) {
            $defaultStage = CrmPipelineStage::query()
            ->where('pipeline_id', $data['pipeline_id'])
            ->where('is_default', true)
            ->firstOrFail();

            $data['pipeline_stage_id'] = $defaultStage->id;

            $stageChanged = true;
        }

        $lead->update($data);

        $lead = $lead->fresh([
            'source',
            'pipeline',
            'stage',
            'assignee',
            'creator',
            'branch',
        ]);

        if ($stageChanged) {
            event(new LeadStageChanged(
                lead: $lead,
                oldStageId: null,
                newStageId: $lead->pipeline_stage_id
            ));
        }

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | CHANGE STAGE
    |--------------------------------------------------------------------------
    */
    public function changeStage(CrmLead $lead, int $stageId): CrmLead
    {
        $stage = CrmPipelineStage::query()->findOrFail($stageId);

        $oldStageId = $lead->pipeline_stage_id;

        if ($oldStageId === $stage->id) {
            return $lead;
        }

        $lead->update([
            'pipeline_id' => $stage->pipeline_id,
            'pipeline_stage_id' => $stage->id,
        ]);

        $lead = $lead->fresh(['pipeline', 'stage']);

        event(new LeadStageChanged(
            lead: $lead,
            oldStageId: $oldStageId,
            newStageId: $stage->id
        ));

        return $lead;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(CrmLead $lead): bool
    {
        return $lead->delete();
    }

    /*
|--------------------------------------------------------------------------
| FILTER DATA
|--------------------------------------------------------------------------
*/
    public function getFilterData(): array
    {
        return [
            'sources' => CrmLeadSource::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'value' => $item->id,
                'label' => $item->name,
            ]),

            'pipelines' => CrmPipeline::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'value' => $item->id,
                'label' => $item->name,
            ]),

            // 👤 ASSIGNEE (USER FILTER)
            'assignees' => User::query()
            ->orderBy('name')
            ->get()
            ->map(fn ($user) => [
                'value' => $user->id,
                'label' => $user->name,
            ]),

            // 🔥 TEMPERATURE FILTER
            'temperatures' => collect([
                CrmPipelineStage::TEMP_COLD,
                CrmPipelineStage::TEMP_WARM,
                CrmPipelineStage::TEMP_HOT,
                CrmPipelineStage::TEMP_CUSTOMER,
                CrmPipelineStage::TEMP_LOST,
            ])->map(fn ($temp) => [
                'value' => $temp,
                'label' => match ($temp) {
                    CrmPipelineStage::TEMP_COLD => 'Cold',
                    CrmPipelineStage::TEMP_WARM => 'Warm',
                    CrmPipelineStage::TEMP_HOT => 'Hot',
                    CrmPipelineStage::TEMP_CUSTOMER => 'Customer',
                    CrmPipelineStage::TEMP_LOST => 'Lost',
                    default => ucfirst($temp),
                    },
                ]),
            ];
        }

        /*
|--------------------------------------------------------------------------
| CREATE DATA
|--------------------------------------------------------------------------
*/
        public function getCreateData(): array
        {
            return [
                'sources' => CrmLeadSource::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

                'pipelines' => CrmPipeline::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

                'users' => User::query()
                ->orderBy('name')
                ->get(),

                'branches' => Branch::query()
                ->orderBy('name')
                ->get(),

                'provinces' => $this->region->provinces(),

                'cities' => [],

                'districts' => [],
            ];
        }

        /*
|--------------------------------------------------------------------------
| UPDATE DATA
|--------------------------------------------------------------------------
*/
        public function getUpdateData(CrmLead $lead): array
        {
            $provinceCode = $lead->province_code;
            $cityCode = $lead->city_code;

            return [
                'lead' => $lead->load([
                    'source',
                    'pipeline',
                    'stage',
                    'assignee',
                    'creator',
                    'branch',
                ]),

                'sources' => CrmLeadSource::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

                'pipelines' => CrmPipeline::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

                'users' => User::query()
                ->orderBy('name')
                ->get(),

                'branches' => Branch::query()
                ->orderBy('name')
                ->get(),

                'provinces' => $this->region->provinces(),

                'cities' => $provinceCode
                ? $this->region->regencies($provinceCode)
                : [],

                'districts' => $cityCode
                ? $this->region->districts($cityCode)
                : [],
            ];
        }
        /*
    |--------------------------------------------------------------------------
    | LEAD CODE
    |--------------------------------------------------------------------------
    */
        private function generateLeadCode(): string
        {
            $year = now()->format('Y');

            $lastLead = CrmLead::query()->latest('id')->first();

            $nextNumber = $lastLead ? $lastLead->id + 1 : 1;

            return sprintf('LD-%s-%05d', $year, $nextNumber);
        }
    }