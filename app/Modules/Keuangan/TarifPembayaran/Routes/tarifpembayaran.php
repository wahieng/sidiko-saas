<?php

use App\Modules\Keuangan\TarifPembayaran\Controllers\TarifPembayaranController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('tarif-pembayaran')
    ->name('tarif-pembayaran.')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/', [
            TarifPembayaranController::class,
            'index',
        ])->name('index');

        Route::get('/{id}', [
            TarifPembayaranController::class,
            'show',
        ])->name('show');

        Route::post('/', [
            TarifPembayaranController::class,
            'store',
        ])->name('store');

        Route::put('/{id}', [
            TarifPembayaranController::class,
            'update',
        ])->name('update');

        Route::delete('/{id}', [
            TarifPembayaranController::class,
            'destroy',
        ])->name('destroy');
    });