<?php

return [

    /*
    |--------------------------------------------------------------------------
    | TASK RESULT RULES
    |--------------------------------------------------------------------------
    */
    'task_result' => [

        'Tidak Merespon' => [
            'actions' => [
                [
                    'type' => 'create_task',
                    'title' => 'Follow Up Ulang (H+1)',
                    'delay' => '+1 day',
                ],
                [
                    'type' => 'move_stage',
                    'stage' => 'Lost',
                ],
            ],
        ],

        'Minta Info' => [
            'actions' => [
                [
                    'type' => 'create_task',
                    'title' => 'Kirim Detail Produk',
                    'delay' => '+2 hours',
                ],
                [
                    'type' => 'move_stage',
                    'stage' => 'Contacted',
                ],
            ],
        ],

        'Pengajuan Masuk' => [
            'actions' => [
                [
                    'type' => 'create_task',
                    'title' => 'Verifikasi Data Customer',
                    'delay' => '+1 hour',
                ],
                [
                    'type' => 'move_stage',
                    'stage' => 'Prospek',
                ],
            ],
        ],

        'Sedang Survey' => [
            'actions' => [
                [
                    'type' => 'create_task',
                    'title' => 'Follow Up Hasil Survey',
                    'delay' => '+1 day',
                ],
                [
                    'type' => 'move_stage',
                    'stage' => 'Survey',
                ],
            ],
        ],

        'DP Masuk' => [
            'actions' => [
                [
                    'type' => 'create_task',
                    'title' => 'Siapkan Delivery',
                    'delay' => 'now',
                ],
                [
                    'type' => 'move_stage',
                    'stage' => 'Deal',
                ],
            ],
        ],

        'Berhasil Closing' => [
            'actions' => [
                [
                    'type' => 'create_task',
                    'title' => 'After Sales Follow Up',
                    'delay' => '+7 days',
                ],
                [
                    'type' => 'move_stage',
                    'stage' => 'Won',
                ],
            ],
        ],
    ],
];