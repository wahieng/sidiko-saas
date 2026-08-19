<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Subscription Status
    |--------------------------------------------------------------------------
    */

    'default_status' => 'active',

    /*
    |--------------------------------------------------------------------------
    | Subscription Status
    |--------------------------------------------------------------------------
    */

    'statuses' => [
        'trial',
        'active',
        'past_due',
        'expired',
        'suspended',
        'cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Access Rules
    |--------------------------------------------------------------------------
    |
    | Menentukan akses aplikasi berdasarkan status subscription.
    |
    */

    'access' => [

        'trial' => [
            'mode' => 'full',
        ],

        'active' => [
            'mode' => 'full',
        ],

        'past_due' => [
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

        'suspended' => [
            'mode' => 'restricted',

            'allowed_routes' => [
                'dashboard',
                'subscription.*',
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