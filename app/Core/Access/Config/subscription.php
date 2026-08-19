<?php

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP Method → Permission Action
    |--------------------------------------------------------------------------
    |
    | Core Access menggunakan HTTP method sebagai default action permission.
    |
    */

    'method_map' => [

        'GET' => 'view',

        'POST' => 'create',

        'PUT' => 'update',

        'PATCH' => 'update',

        'DELETE' => 'delete',

    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Actions
    |--------------------------------------------------------------------------
    */

    'actions' => [
        'view',
        'create',
        'update',
        'delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Permission
    |--------------------------------------------------------------------------
    |
    | Jika true, PermissionMiddleware akan menentukan permission
    | berdasarkan route + HTTP method secara otomatis.
    |
    */

    'automatic' => true,

];