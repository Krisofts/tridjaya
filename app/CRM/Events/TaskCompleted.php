<?php

namespace App\CRM\Events;

use App\CRM\Models\CrmTask;

class TaskCompleted
{
    public function __construct(
        public CrmTask $task,

        // 👉 ganti string jadi ID result dari tabel crm_results
        public ?int $result_id = null
    ) {}
}