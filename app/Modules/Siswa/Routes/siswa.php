<?php

use App\Modules\Siswa\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'tenant'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        Route::get('/', [
            SiswaController::class,
            'index',
        ])->name('index');

        Route::post('/', [
            SiswaController::class,
            'store',
        ])->name('store');

        Route::get('/{siswa}', [
            SiswaController::class,
            'show',
        ])->name('show');

        Route::put('/{siswa}', [
            SiswaController::class,
            'update',
        ])->name('update');

        Route::delete('/{siswa}', [
            SiswaController::class,
            'destroy',
        ])->name('destroy');
    });

/*
|--------------------------------------------------------------------------
| Siswa Tahun
|--------------------------------------------------------------------------
*/

require __DIR__ . '/siswatahun.php';

/*
|--------------------------------------------------------------------------
| Orang Tua
|--------------------------------------------------------------------------
*/

require __DIR__ . '/orangtua.php';

/*
|--------------------------------------------------------------------------
| Wali
|--------------------------------------------------------------------------
*/

require __DIR__ . '/wali.php';

/*
|--------------------------------------------------------------------------
| Dokumen Siswa
|--------------------------------------------------------------------------
*/

require __DIR__ . '/dokumensiswa.php';