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

// Identity
require base_path(
    'app/Core/Identity/Routes/web.php'
);

require base_path(
    'app/Core/Identity/Routes/auth.php'
);

// Tenant
require base_path(
    'app/Core/Tenant/Routes/web.php'
);

// Subscription
require base_path(
    'app/Core/Subscription/Routes/subscription.php'
);

// Billing
require base_path(
    'app/Core/Billing/Routes/billing.php'
);

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

// Tahun Ajaran
require base_path(
    'app/Modules/Akademik/TahunAjaran/Routes/tahunajaran.php'
);

// Semester
require base_path(
    'app/Modules/Akademik/Semester/Routes/semester.php'
);

// Rombel
require base_path(
    'app/Modules/Akademik/Rombel/Routes/rombel.php'
);

// Kelompok Rombel
require base_path(
    'app/Modules/Akademik/KelompokRombel/Routes/kelompokrombel.php'
);

/*
|--------------------------------------------------------------------------
| Siswa
|--------------------------------------------------------------------------
*/

require base_path(
    'app/Modules/Siswa/Siswa/Routes/siswa.php'
);

/*
|--------------------------------------------------------------------------
| Keuangan
|--------------------------------------------------------------------------
*/

// Jenis Pembayaran
require base_path(
    'app/Modules/Keuangan/JenisPembayaran/Routes/jenispembayaran.php'
);

// Tarif Pembayaran
require base_path(
    'app/Modules/Keuangan/TarifPembayaran/Routes/tarifpembayaran.php'
);