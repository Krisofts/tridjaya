<?php

namespace Database\Seeders;

use App\CRM\Models\CrmLostReason;
use App\CRM\Models\CrmPipeline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CrmLostReasonSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // =========================================================
            // CASH LOST REASONS
            // =========================================================
            'Cash' => [
                ['name' => 'Harga terlalu mahal',        'sort_order' => 1],
                ['name' => 'Beli di tempat lain',        'sort_order' => 2],
                ['name' => 'Tidak jadi beli',            'sort_order' => 3],
                ['name' => 'Tidak dihubungi',            'sort_order' => 4],
                ['name' => 'Stok tidak tersedia',        'sort_order' => 5],
                ['name' => 'Batal tanpa alasan',         'sort_order' => 6],
            ],

            // =========================================================
            // KREDIT LOST REASONS
            // =========================================================
            'Kredit' => [
                ['name' => 'Ditolak leasing',                   'sort_order' => 1],
                ['name' => 'DP tidak sanggup',                  'sort_order' => 2],
                ['name' => 'Kredit tidak disetujui',            'sort_order' => 3],
                ['name' => 'Data tidak lengkap',                'sort_order' => 4],
                ['name' => 'Pindah ke cash / kompetitor',       'sort_order' => 5],
                ['name' => 'Tidak dihubungi',                   'sort_order' => 6],
            ],
        ];

        // Load semua pipeline sekaligus — hindari N+1 query
        $pipelines = CrmPipeline::whereIn('name', array_keys($data))
            ->pluck('id', 'name');

        foreach ($data as $pipelineName => $reasons) {

            if (! $pipelines->has($pipelineName)) {
                $this->command->warn("⚠  Pipeline [{$pipelineName}] tidak ditemukan, dilewati.");
                continue;
            }

            $pipelineId = $pipelines->get($pipelineName);

            foreach ($reasons as $item) {
                $slug = Str::slug($item['name']);

                CrmLostReason::updateOrCreate(
                    [
                        'pipeline_id' => $pipelineId,
                        'slug'        => $slug,
                    ],
                    [
                        'name'       => $item['name'],
                        'sort_order' => $item['sort_order'],
                        'is_active'  => true,
                        'is_default' => false,
                    ]
                );
            }

            $this->command->info("✓ Lost reasons [{$pipelineName}] seeded.");
        }
    }
}