<?php

use App\Core\Billing\Controllers\BillingController;
use App\Core\Billing\Controllers\PembayaranBillingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BILLING
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('billing')
    ->name('core.billing.')
    ->group(function () {

        Route::get('/', [
            BillingController::class,
            'index',
        ])->name('index');

        Route::get('/{tagihan}', [
            BillingController::class,
            'show',
        ])->name('show');

        Route::post('/generate', [
            BillingController::class,
            'generate',
        ])->name('generate');
    });

/*
|--------------------------------------------------------------------------
| PEMBAYARAN
|--------------------------------------------------------------------------
|
| URL tetap:
| billing/{tagihan}/pembayaran
|
| Route name:
| core.pembayaran.index
| core.pembayaran.store
|
*/

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('billing')
    ->name('core.pembayaran.')
    ->group(function () {

        Route::get('/{tagihan}/pembayaran', [
            PembayaranBillingController::class,
            'index',
        ])->name('index');

        Route::post('/{tagihan}/pembayaran', [
            PembayaranBillingController::class,
            'store',
        ])->name('store');
    });