<?php

namespace App\CRM\Models;

use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmActivity extends Model
{
    protected $table = 'crm_activities';

    // -------------------------------------------------------------------------
    // TYPE CONSTANTS
    // -------------------------------------------------------------------------

    public const TYPE_WHATSAPP = 'whatsapp';
    public const TYPE_CALL     = 'call';
    public const TYPE_VISIT    = 'visit';
    public const TYPE_SURVEY   = 'survey';
    public const TYPE_NOTE     = 'note';

    // -------------------------------------------------------------------------
    // FILLABLE & CASTS
    // -------------------------------------------------------------------------

    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'title',
        'description',
        'result_id',
        'next_follow_up_at',
        'stage_id',
    ];

    protected $casts = [
        'next_follow_up_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // RELATIONS
    // -------------------------------------------------------------------------

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(CrmPipelineStage::class, 'stage_id');
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(CrmResult::class, 'result_id');
    }
}