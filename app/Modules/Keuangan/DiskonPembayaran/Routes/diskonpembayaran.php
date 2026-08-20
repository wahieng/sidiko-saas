<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Keuangan\DiskonPembayaran\Controllers\DiskonPembayaranController;

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('keuangan/diskon-pembayaran')
    ->name('keuangan.diskon-pembayaran.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Diskon Pembayaran
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            DiskonPembayaranController::class,
            'index',
        ])->name('index');

        Route::post('/', [
            DiskonPembayaranController::class,
            'store',
        ])->name('store');

        Route::get('/{diskonPembayaran}', [
            DiskonPembayaranController::class,
            'show',
        ])->name('show');

        Route::put('/{diskonPembayaran}', [
            DiskonPembayaranController::class,
            'update',
        ])->name('update');

        Route::delete('/{diskonPembayaran}', [
            DiskonPembayaranController::class,
            'destroy',
        ])->name('destroy');

        Route::post('/{diskonPembayaran}/toggle-status', [
            DiskonPembayaranController::class,
            'toggleStatus',
        ])->name('toggle-status');

        /*
        |--------------------------------------------------------------------------
        | Berdasarkan Siswa
        |--------------------------------------------------------------------------
        */

        Route::get('/siswa/{siswa}', [
            DiskonPembayaranController::class,
            'bySiswa',
        ])->name('siswa');

        Route::get('/siswa/{siswa}/aktif', [
            DiskonPembayaranController::class,
            'activeBySiswa',
        ])->name('siswa-aktif');
    });
