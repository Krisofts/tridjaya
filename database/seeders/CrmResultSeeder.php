<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmResult;

class CrmResultSeeder extends Seeder
{
    public function run(): void
    {
        $results = [
            [
                'name' => 'Tidak Merespon',
                'code' => 'no_response',
            ],
            [
                'name' => 'Tertarik',
                'code' => 'interested',
            ],
            [
                'name' => 'Tidak Tertarik',
                'code' => 'not_interested',
            ],
            [
                'name' => 'Follow Up Kembali',
                'code' => 'follow_up',
            ],

            // 👉 FLOW QUALIFIED → PROSPECT SUPPORT
            [
                'name' => 'Qualified',
                'code' => 'qualified',
            ],
            [
                'name' => 'Prospek',
                'code' => 'prospect',
            ],

            [
                'name' => 'Pengajuan Kredit',
                'code' => 'submitted',
            ],
            [
                'name' => 'Sedang Survey',
                'code' => 'survey',
            ],
            [
                'name' => 'Disetujui',
                'code' => 'approved',
            ],
            [
                'name' => 'Ditolak',
                'code' => 'rejected',
            ],

            [
                'name' => 'Deal Berjalan',
                'code' => 'deal',
            ],
            [
                'name' => 'DP Masuk',
                'code' => 'dp',
            ],
            [
                'name' => 'Berhasil Closing',
                'code' => 'success',
            ],
        ];

        foreach ($results as $item) {
            CrmResult::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name' => $item['name'],
                    'is_active' => true,

                    // 👉 hanya success yang terminal (bisa kamu expand nanti ke rejected juga)
                    'is_terminal' => in_array($item['code'], ['success']),
                ]
            );
        }
    }
}