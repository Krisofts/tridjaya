<?php

namespace App\CRM\Services;

use App\CRM\Filters\LeadFilter;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmLeadSource;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use App\Models\Branch;
use App\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LeadService
{
    public function __construct(
        protected LeadFilter $filter
    ) {}

    /* -------------------------------------------------
     | PAGINATION + FILTER
     |-------------------------------------------------*/
    public function getPaginated(
        ?string $search = null,
        ?int $sourceId = null,
        ?int $pipelineId = null,
        ?int $stageId = null,
        int $perPage = 15
    ): LengthAwarePaginator {

        $query = CrmLead::query()
            ->with([
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
            'stage_id' => $stageId,
        ]);

        return $query
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /* -------------------------------------------------
     | DETAIL
     |-------------------------------------------------*/
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

    /* -------------------------------------------------
     | CREATE
     |-------------------------------------------------*/
    public function create(array $data): CrmLead
    {
        $data['lead_code'] = $this->generateLeadCode();

        $defaultStage = CrmPipelineStage::query()
            ->where('pipeline_id', $data['pipeline_id'])
            ->where('is_default', true)
            ->firstOrFail();

        $data['pipeline_stage_id'] = $defaultStage->id;

        return CrmLead::create($data);
    }

    /* -------------------------------------------------
     | UPDATE
     |-------------------------------------------------*/
    public function update(CrmLead $lead, array $data): CrmLead
    {
        if (
            isset($data['pipeline_id']) &&
            $data['pipeline_id'] != $lead->pipeline_id
        ) {
            $defaultStage = CrmPipelineStage::query()
                ->where('pipeline_id', $data['pipeline_id'])
                ->where('is_default', true)
                ->firstOrFail();

            $data['pipeline_stage_id'] = $defaultStage->id;
        }

        $lead->update($data);

        return $lead->fresh([
            'source',
            'pipeline',
            'stage',
            'assignee',
            'creator',
            'branch',
        ]);
    }

    /* -------------------------------------------------
     | CHANGE STAGE
     |-------------------------------------------------*/
    public function changeStage(CrmLead $lead, int $stageId): CrmLead
    {
        $stage = CrmPipelineStage::query()
            ->findOrFail($stageId);

        $lead->update([
            'pipeline_id' => $stage->pipeline_id,
            'pipeline_stage_id' => $stage->id,
        ]);

        return $lead->fresh([
            'pipeline',
            'stage',
        ]);
    }

    /* -------------------------------------------------
     | DELETE
     |-------------------------------------------------*/
    public function delete(CrmLead $lead): bool
    {
        return $lead->delete();
    }

    /* -------------------------------------------------
     | FILTER DATA (INDEX PAGE DROPDOWN)
     |-------------------------------------------------*/
    public function getFilterData(): array
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
        ];
    }

    /* -------------------------------------------------
     | CREATE PAGE DATA
     |-------------------------------------------------*/
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
        ];
    }

    /* -------------------------------------------------
     | UPDATE PAGE DATA
     |-------------------------------------------------*/
    public function getUpdateData(CrmLead $lead): array
    {
        return [
            'lead' => $lead->load([
                'source',
                'pipeline',
                'stage',
                'assignee',
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
        ];
    }

    /* -------------------------------------------------
     | LEAD CODE GENERATOR
     |-------------------------------------------------*/
    private function generateLeadCode(): string
    {
        $year = now()->format('Y');

        $lastLead = CrmLead::query()
            ->latest('id')
            ->first();

        $nextNumber = $lastLead
            ? $lastLead->id + 1
            : 1;

        return sprintf(
            'LD-%s-%05d',
            $year,
            $nextNumber
        );
    }
}