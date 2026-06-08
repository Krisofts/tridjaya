<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LEAD STATUS (PIPELINE)
    |--------------------------------------------------------------------------
    */
    'lead_status' => [
        'new'        => 'Baru',
        'contacted'  => 'Sudah Dihubungi',
        'qualified'  => 'Prospek Potensial',
        'proposal'   => 'Penawaran Dikirim',
        'deal'       => 'Deal (Siap Transaksi)',
        'won'        => 'Closed Deal',
        'lost'       => 'Gagal (Tidak Jadi)',
    ],

    /*
    |--------------------------------------------------------------------------
    | LEAD SOURCE (MARKETING CHANNEL)
    |--------------------------------------------------------------------------
    */
    'lead_source' => [
        'whatsapp'  => 'WhatsApp',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'tiktok'    => 'TikTok',
        'ads'       => 'Iklan (Ads)',
        'website'   => 'Website',
        'walk_in'   => 'Walk In',
    ],

    /*
    |--------------------------------------------------------------------------
    | LEAD INTEREST (PRODUCT CATEGORY)
    |--------------------------------------------------------------------------
    */
    'lead_interest' => [
        'tv'             => 'TV',
        'hp'             => 'HP / Smartphone',
        'kulkas'         => 'Kulkas',
        'mesin_cuci'     => 'Mesin Cuci',
        'sepeda_listrik' => 'Sepeda Listrik',
        'ac'             => 'AC',
        'speaker'        => 'Speaker',
        'freezer'        => 'Freezer',
        'showcase'       => 'Showcase',
        'sofa'           => 'Sofa',
        'lemari'         => 'Lemari',
        'cator'          => 'Cator',
        'alat_tani'      => 'Alat Tani',
        'motor_honda'    => 'Motor Honda',
        'laptop'         => 'Laptop',
    ],

    /*
    |--------------------------------------------------------------------------
    | TASK STATUS
    |--------------------------------------------------------------------------
    */
    'task_status' => [
        'open'        => 'Open',
        'in_progress' => 'In Progress',
        'done'        => 'Done',
        'cancelled'   => 'Cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | TASK PRIORITY
    |--------------------------------------------------------------------------
    */
    'task_priority' => [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ],

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION TYPE (🔥 NEW - CORE CRM FLOW)
    |--------------------------------------------------------------------------
    */
    'transaction_type' => [
        'cash'        => 'Cash Payment',
        'credit'      => 'Credit Purchase',
        'down_payment'=> 'Down Payment',
        'installment' => 'Installment',
        'refund'      => 'Refund',
    ],

    /*
    |--------------------------------------------------------------------------
    | TRANSACTION STATUS
    |--------------------------------------------------------------------------
    */
    'transaction_status' => [
        'pending'       => 'Pending',
        'partial'       => 'Partial Paid',
        'paid'          => 'Paid',
        'failed'        => 'Failed',
        'cancelled'     => 'Cancelled',
    ],

];