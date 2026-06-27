<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Login Redirect By Group
    |--------------------------------------------------------------------------
    */

    'groups' => [

        'superadmin' => 'dashboard/sales',

        'owner'      => 'dashboard/sales',

        'manager'    => 'dashboard/sales',

        'hrd'        => 'dashboard/sales',

        'finance'    => 'dashboard/sales',

        'admin'      => 'dashboard/sales',

        'sales'      => 'crm/my-leads',

        'support'    => 'support.dashboard',

        'driver'     => 'driver.dashboard',

        'pdi'        => 'pdi.dashboard',

        'user'       => 'dashboard',

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Redirect
    |--------------------------------------------------------------------------
    */

    'default' => 'dashboard',

];