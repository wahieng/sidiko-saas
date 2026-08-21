<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Keuangan\Tagihan\Controllers\TagihanController;

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('keuangan/tagihan')
    ->name('keuangan.tagihan.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Tagihan
        |--------------------------------------------------------------------------
        */

        Route::get('/', [
            TagihanController::class,
            'index',
        ])->name('index');

        Route::post('/', [
            TagihanController::class,
            'store',
        ])->name('store');

        Route::get('/{tagihan}', [
            TagihanController::class,
            'show',
        ])->name('show');

        Route::put('/{tagihan}', [
            TagihanController::class,
            'update',
        ])->name('update');

        Route::delete('/{tagihan}', [
            TagihanController::class,
            'destroy',
        ])->name('destroy');
    });