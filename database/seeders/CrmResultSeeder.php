<?php

namespace Database\Seeders;

use App\CRM\Models\CrmResult;
use Illuminate\Database\Seeder;

class CrmResultSeeder extends Seeder
{
    public function run(): void
    {
        $results = [
            // Tidak merespon / belum siap
            ['name' => 'Tidak Merespon',         'code' => 'no_response',       'is_terminal' => false],
            ['name' => 'Tidak Bisa Dihubungi',   'code' => 'unreachable',       'is_terminal' => false],
            ['name' => 'Follow Up Kembali',      'code' => 'follow_up',         'is_terminal' => false],
            ['name' => 'Belum Siap Beli',        'code' => 'not_ready',         'is_terminal' => false],
            ['name' => 'Tidak Tertarik',         'code' => 'not_interested',    'is_terminal' => false],

            // Tertarik / dalam proses
            ['name' => 'Tertarik',               'code' => 'interested',        'is_terminal' => false],
            ['name' => 'Minta Info Lebih',       'code' => 'need_info',         'is_terminal' => false],
            ['name' => 'Simulasi Dikirim',       'code' => 'simulation_sent',   'is_terminal' => false],
            ['name' => 'Penawaran Dikirim',      'code' => 'offer_sent',        'is_terminal' => false],
            ['name' => 'Negosiasi Harga',        'code' => 'negotiating',       'is_terminal' => false],

            // Proses dokumen & pengajuan
            ['name' => 'Dokumen Lengkap',        'code' => 'docs_complete',     'is_terminal' => false],
            ['name' => 'Dokumen Kurang',         'code' => 'docs_incomplete',   'is_terminal' => false],
            ['name' => 'Pengajuan Dikirim',      'code' => 'submitted',         'is_terminal' => false],
            ['name' => 'Sedang Disurvey',        'code' => 'survey',            'is_terminal' => false],
            ['name' => 'Disetujui Leasing',      'code' => 'approved',          'is_terminal' => false],
            ['name' => 'Ditolak Leasing',        'code' => 'rejected',          'is_terminal' => false],

            // Closing
            ['name' => 'SPK Ditandatangani',     'code' => 'spk_signed',        'is_terminal' => false],
            ['name' => 'DP Masuk',               'code' => 'dp',                'is_terminal' => false],
            ['name' => 'Pembayaran Lunas',       'code' => 'paid',              'is_terminal' => false],
            ['name' => 'Berhasil Closing',       'code' => 'success',           'is_terminal' => true],

            // Gagal
            ['name' => 'Batal oleh Customer',   'code' => 'cancelled',         'is_terminal' => true],
            ['name' => 'Pindah ke Kompetitor',  'code' => 'competitor',        'is_terminal' => true],
        ];

        foreach ($results as $index => $item) {
            CrmResult::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name'        => $item['name'],
                    'sort_order'  => $index + 1,
                    'is_active'   => true,
                    'is_terminal' => $item['is_terminal'],
                ],
            );
        }

        $this->command->info('✓ Results berhasil di-seed (' . count($results) . ' items).');
    }
}