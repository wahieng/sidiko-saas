<?php

use App\Modules\Akademik\KelompokRombel\Controllers\KelompokRombelController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('akademik/kelompok-rombel')
    ->name('akademik.kelompok-rombel.')
    ->group(function () {

        Route::get('/', [KelompokRombelController::class, 'index'])
            ->name('index');

        Route::post('/', [KelompokRombelController::class, 'store'])
            ->name('store');

        Route::get('/{kelompokRombel}', [
            KelompokRombelController::class,
            'show',
        ])->name('show');

        Route::put('/{kelompokRombel}', [
            KelompokRombelController::class,
            'update',
        ])->name('update');

        Route::delete('/{kelompokRombel}', [
            KelompokRombelController::class,
            'destroy',
        ])->name('destroy');

        Route::get(
            '/tahun-ajaran/{tahunAjaran}',
            [KelompokRombelController::class, 'byTahunAjaran']
        )->name('by-tahun-ajaran');

        Route::get(
            '/tahun-ajaran/{tahunAjaran}/rombel/{rombel}',
            [KelompokRombelController::class, 'byRombel']
        )->name('by-rombel');
    });