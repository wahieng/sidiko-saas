<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Core Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Core Modules
|--------------------------------------------------------------------------
*/

require base_path('app/Core/Identity/Routes/web.php');
require base_path('app/Core/Identity/Routes/auth.php');

require base_path('app/Core/Tenant/Routes/web.php');

require base_path('app/Core/Subscription/Routes/subscription.php');
require base_path('app/Core/Billing/Routes/billing.php');

/*
|--------------------------------------------------------------------------
| Application Modules
|--------------------------------------------------------------------------
*/

    /*
    |--------------------------------------------------------------------------
    | Akademik
    |--------------------------------------------------------------------------
    */

    require base_path(
        'app/Modules/Akademik/TahunAjaran/Routes/tahunajaran.php'
    );

    require base_path(
        'app/Modules/Akademik/Semester/Routes/semester.php'
    );

    require base_path(
        'app/Modules/Akademik/Rombel/Routes/rombel.php'
    );

    require base_path(
        'app/Modules/Akademik/KelompokRombel/Routes/kelompokrombel.php'
    );
require base_path('app/Modules/Siswa/Siswa/Routes/siswa.php');
require base_path('app/Modules/Keuangan/JenisPembayaran/Routes/jenispembayaran.php');