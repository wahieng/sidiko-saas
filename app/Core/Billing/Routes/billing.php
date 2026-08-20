<?php

use App\Core\Billing\Controllers\BillingController;
use App\Core\Billing\Controllers\PembayaranBillingController;
use Illuminate\Support\Facades\Route;

Route::middleware([
        'auth',
        'tenant',
        'subscription',
        'permission',
    ])
    ->prefix('billing')
    ->name('billing.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | BILLING
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            BillingController::class,
            'index'
        ])->name('index');

        Route::get('/{tagihan}', [
            BillingController::class,
            'show'
        ])->name('show');

        Route::post('/generate', [
            BillingController::class,
            'generate'
        ])->name('generate');


        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        Route::get('/{tagihan}/pembayaran', [
            PembayaranBillingController::class,
            'index'
        ])->name('pembayaran.index');

        Route::post('/{tagihan}/pembayaran', [
            PembayaranBillingController::class,
            'store'
        ])->name('pembayaran.store');

    });