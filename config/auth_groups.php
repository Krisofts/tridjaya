<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Group
    |--------------------------------------------------------------------------
    */
    'defaultGroup' => 'user',

    /*
    |--------------------------------------------------------------------------
    | Super Admin Group
    |--------------------------------------------------------------------------
    */
    'superadminGroup' => 'superadmin',

    /*
    |--------------------------------------------------------------------------
    | Group Definitions
    |--------------------------------------------------------------------------
    */
    'groups' => [



        'owner' => [
            'title' => 'Owner',
            'description' => 'Full business access',
        ],

        'manager' => [
            'title' => 'Manager',
            'description' => 'Manage operations and monitoring',
        ],

        'hrd' => [
            'title' => 'HRD',
            'description' => 'Manage employees and HR data',
        ],

        'finance' => [
            'title' => 'Finance',
            'description' => 'Manage financial data',
        ],

        'admin' => [
            'title' => 'Admin',
            'description' => 'Manage system data',
        ],

        'sales' => [
            'title' => 'Sales',
            'description' => 'Manage leads and sales activities',
        ],

        'support' => [
            'title' => 'Support',
            'description' => 'Handle customer support',
        ],

        'driver' => [
            'title' => 'Driver',
            'description' => 'Delivery and transport duties',
        ],

        'pdi' => [
            'title' => 'PDI',
            'description' => 'Pre delivery inspection',
        ],

        'user' => [
            'title' => 'User',
            'description' => 'Default user',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Definitions
    |--------------------------------------------------------------------------
    */
    'permissions' => [

        'dashboard.view' => 'View dashboard',

        'admin.access' => 'Access admin panel',

        'users.view' => 'View users',
        'users.create' => 'Create users',
        'users.update' => 'Update users',
        'users.delete' => 'Delete users',

        'leads.view' => 'View leads',
        'leads.create' => 'Create leads',
        'leads.update' => 'Update leads',
        'leads.delete' => 'Delete leads',
        'leads.assign' => 'Assign leads',
        'leads.followup' => 'Manage follow up',
        'leads.activities' => 'Manage lead activities',

        'beta.access' => 'Access beta features',
    ],

    /*
    |--------------------------------------------------------------------------
    | Group Permissions
    |--------------------------------------------------------------------------
    */
    'groupPermissions' => [

        'superadmin' => [
            '*',
        ],

        'owner' => [
            '*',
        ],

        'manager' => [
            'dashboard.view',
            'users.view',
            'leads.*',
        ],

        'hrd' => [
            'dashboard.view',
            'users.*',
        ],

        'finance' => [
            'dashboard.view',
        ],

        'admin' => [
            'dashboard.view',
            'admin.access',
            'users.*',
            'leads.*',
        ],

        'sales' => [
            'dashboard.view',
            'leads.view',
            'leads.create',
            'leads.update',
            'leads.followup',
            'leads.activities',
        ],

        'support' => [
            'dashboard.view',
            'leads.view',
            'leads.followup',
            'leads.activities',
        ],

        'driver' => [
            'dashboard.view',
        ],

        'pdi' => [
            'dashboard.view',
        ],

        'user' => [
            'dashboard.view',
        ],

    ],

];