<?php

use App\Modules\Siswa\OrangTua\Controllers\OrangTuaController;
use Illuminate\Support\Facades\Route;

Route::middleware([
        'auth',
        'tenant',
        'subscription',
        'permission',
    ])
    ->prefix('orang-tua')
    ->name('orang-tua.')
    ->group(function () {

        Route::get('/', [OrangTuaController::class, 'index'])
            ->name('index');

        Route::post('/', [OrangTuaController::class, 'store'])
            ->name('store');

        Route::get('/{orangTua}', [OrangTuaController::class, 'show'])
            ->name('show');

        Route::put('/{orangTua}', [OrangTuaController::class, 'update'])
            ->name('update');

        Route::delete('/{orangTua}', [OrangTuaController::class, 'destroy'])
            ->name('destroy');
    });