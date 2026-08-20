<?php

use App\Modules\Siswa\Wali\Controllers\WaliController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('{siswa}/wali')
    ->name('siswa.wali.')
    ->group(function () {

        Route::get('/', [WaliController::class, 'index'])
            ->name('index');

        Route::post('/', [WaliController::class, 'store'])
            ->name('store');

        Route::get('/{wali}', [WaliController::class, 'show'])
            ->name('show');

        Route::put('/{wali}', [WaliController::class, 'update'])
            ->name('update');

        Route::delete('/{wali}', [WaliController::class, 'destroy'])
            ->name('destroy');
    });
