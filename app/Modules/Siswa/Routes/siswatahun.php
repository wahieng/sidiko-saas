<?php

use App\Modules\Siswa\Controllers\SiswaTahunController;
use Illuminate\Support\Facades\Route;

Route::prefix('siswa-tahun')
    ->name('siswa_tahun.')
    ->group(function () {

        Route::get('/', [
            SiswaTahunController::class,
            'index',
        ])->name('index');

        Route::post('/', [
            SiswaTahunController::class,
            'store',
        ])->name('store');

        Route::get('/{siswaTahun}', [
            SiswaTahunController::class,
            'show',
        ])->name('show');

        Route::put('/{siswaTahun}', [
            SiswaTahunController::class,
            'update',
        ])->name('update');

        Route::delete('/{siswaTahun}', [
            SiswaTahunController::class,
            'destroy',
        ])->name('destroy');
    });