<?php

use App\Modules\Siswa\Controllers\DokumenSiswaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'tenant'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dokumen Siswa
        |--------------------------------------------------------------------------
        */

        Route::post('/dokumen', [
            DokumenSiswaController::class,
            'store',
        ])->name('dokumen.store');

        Route::get('/{siswa}/dokumen', [
            DokumenSiswaController::class,
            'index',
        ])->name('dokumen.index');

        Route::get('/{siswa}/dokumen/{dokumen}', [
            DokumenSiswaController::class,
            'show',
        ])->name('dokumen.show');

        Route::delete('/{siswa}/dokumen/{dokumen}', [
            DokumenSiswaController::class,
            'destroy',
        ])->name('dokumen.destroy');
    });