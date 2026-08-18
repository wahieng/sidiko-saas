<?php

use App\Modules\Keuangan\JenisPembayaran\Controllers\JenisPembayaranController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('keuangan/jenis-pembayaran')
    ->name('keuangan.jenis-pembayaran.')
    ->group(function () {

        Route::get('/', [JenisPembayaranController::class, 'index'])
            ->name('index');

        Route::post('/', [JenisPembayaranController::class, 'store'])
            ->name('store');

        Route::get('/{jenisPembayaran}', [JenisPembayaranController::class, 'show'])
            ->name('show');

        Route::put('/{jenisPembayaran}', [JenisPembayaranController::class, 'update'])
            ->name('update');

        Route::delete('/{jenisPembayaran}', [JenisPembayaranController::class, 'destroy'])
            ->name('destroy');

    });