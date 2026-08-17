<?php

use App\Modules\Siswa\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {

        Route::get('/', [SiswaController::class, 'index'])
            ->name('index');

        Route::post('/', [SiswaController::class, 'store'])
            ->name('store');

        Route::get('/{siswa}', [SiswaController::class, 'show'])
            ->name('show');

        Route::put('/{siswa}', [SiswaController::class, 'update'])
            ->name('update');

        Route::delete('/{siswa}', [SiswaController::class, 'destroy'])
            ->name('destroy');
    });