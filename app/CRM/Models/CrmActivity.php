<?php

namespace App\CRM\Models;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipelineStage;
use App\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmActivity extends Model
{
    protected $table = 'crm_activities';

    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'title',
        'description',
        'result',
        'next_follow_up_at',
        'stage_id',
    ];

    protected $casts = [
        'next_follow_up_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | CONSTANT TYPE (RECOMMENDED)
    |--------------------------------------------------------------------------
    */
    public const TYPE_WHATSAPP = 'whatsapp';
    public const TYPE_CALL = 'call';
    public const TYPE_VISIT = 'visit';
    public const TYPE_SURVEY = 'survey';
    public const TYPE_NOTE = 'note';

    /*
    |--------------------------------------------------------------------------
    | RESULT (INDONESIA CRM LEASING)
    |--------------------------------------------------------------------------
    */
    public const RESULT_NO_RESPONSE = 'Tidak Merespon';
    public const RESULT_INTERESTED = 'Minta Info';
    public const RESULT_NOT_INTERESTED = 'Tidak Tertarik';
    public const RESULT_FOLLOW_UP = 'Follow Up Kembali';

    public const RESULT_SUBMITTED = 'Pengajuan Masuk';
    public const RESULT_SURVEY = 'Sedang Survey';
    public const RESULT_APPROVED = 'Disetujui';
    public const RESULT_REJECT = 'Ditolak';

    public const RESULT_DEAL = 'Deal Berjalan';
    public const RESULT_DP = 'DP Masuk';
    public const RESULT_SUCCESS = 'Berhasil Closing';
}