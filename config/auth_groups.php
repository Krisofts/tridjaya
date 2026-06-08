<?php

return [

    /*
    |---------------------------------------------------
    | DEFAULT GROUP
    |---------------------------------------------------
    */
    'defaultGroup' => 'user',

    /*
    |---------------------------------------------------
    | GROUP DEFINITIONS
    |---------------------------------------------------
    */
    'groups' => [

        'superadmin' => [
            'title' => 'Super Admin',
            'description' => 'Full access system',
        ],

        'admin' => [
            'title' => 'Admin',
            'description' => 'Manage system data',
        ],

        'user' => [
            'title' => 'User',
            'description' => 'Default user',
        ],

    ],

    /*
    |---------------------------------------------------
    | PERMISSIONS LIST (REFERENCE ONLY)
    |---------------------------------------------------
    */
    'permissions' => [

        /*
        | Admin
        */
        'admin.access' => 'Access admin panel',

        /*
        | Users
        */
        'users.view' => 'View users',
        'users.create' => 'Create users',
        'users.edit' => 'Edit users',
        'users.delete' => 'Delete users',

        /*
        | CRM Leads
        */
        'leads.view' => 'View leads',
        'leads.create' => 'Create leads',
        'leads.edit' => 'Edit leads',
        'leads.delete' => 'Delete leads',
        'leads.assign' => 'Assign leads',
        'leads.followup' => 'Manage follow up',
        'leads.activities' => 'Manage lead activities',

        /*
        | Experimental
        */
        'beta.access' => 'Access beta features',

    ],

    /*
    |---------------------------------------------------
    | GROUP → PERMISSION MATRIX
    |---------------------------------------------------
    */
    'matrix' => [

        /*
        | SUPERADMIN = FULL ACCESS
        */
        'superadmin' => [
            '*',
        ],

        /*
        | ADMIN
        */
        'admin' => [

            'admin.access',

            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            'leads.view',
            'leads.create',
            'leads.edit',
            'leads.delete',
            'leads.assign',
            'leads.followup',
            'leads.activities',
        ],

        /*
        | USER
        */
        'user' => [
            
        ],

    ],

];