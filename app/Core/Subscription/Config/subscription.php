<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Status
    |--------------------------------------------------------------------------
    */

    'statuses' => [

        'trial',
        'active',
        'past_due',
        'suspended',
        'expired',
        'cancelled',

    ],

    /*
    |--------------------------------------------------------------------------
    | Default Status
    |--------------------------------------------------------------------------
    */

    'default_status' => 'active',

    /*
    |--------------------------------------------------------------------------
    | Access Mode
    |--------------------------------------------------------------------------
    |
    | full       = akses normal
    | restricted = hanya route tertentu
    | blocked    = akses aplikasi dibatasi
    |
    */

    'access' => [

        'trial' => [
            'mode' => 'full',

            'allowed_routes' => [
                '*',
            ],
        ],

        'active' => [
            'mode' => 'full',

            'allowed_routes' => [
                '*',
            ],
        ],

        'past_due' => [
            'mode' => 'restricted',

            'allowed_routes' => [
                'dashboard',
                'subscription.*',
                'profile.*',
            ],
        ],

        'suspended' => [
            'mode' => 'restricted',

            'allowed_routes' => [
                'dashboard',
                'subscription.*',
                'profile.*',
            ],
        ],

        'expired' => [
            'mode' => 'restricted',

            'allowed_routes' => [
                'dashboard',
                'subscription.*',
                'profile.*',
            ],
        ],

        'cancelled' => [
            'mode' => 'blocked',

            'allowed_routes' => [
                'subscription.*',
            ],
        ],

    ],

];