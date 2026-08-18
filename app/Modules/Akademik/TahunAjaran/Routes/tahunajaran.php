<?php

use App\Modules\Akademik\TahunAjaran\Controllers\TahunAjaranController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('akademik/tahun-ajaran')
    ->name('akademik.tahun-ajaran.')
    ->group(function () {

        Route::get('/', [TahunAjaranController::class, 'index'])
            ->name('index');

        Route::post('/', [TahunAjaranController::class, 'store'])
            ->name('store');

        Route::get('/{tahunAjaran}', [TahunAjaranController::class, 'show'])
            ->name('show');

        Route::put('/{tahunAjaran}', [TahunAjaranController::class, 'update'])
            ->name('update');

        Route::delete('/{tahunAjaran}', [TahunAjaranController::class, 'destroy'])
            ->name('destroy');

        Route::post('/{tahunAjaran}/aktifkan', [
            TahunAjaranController::class,
            'aktifkan',
        ])->name('aktifkan');
    });