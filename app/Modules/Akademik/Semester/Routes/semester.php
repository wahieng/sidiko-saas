<?php

use App\Modules\Akademik\Semester\Controllers\SemesterController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->prefix('akademik/semester')
    ->name('akademik.semester.')
    ->group(function () {

        Route::get('/aktif', [
            SemesterController::class,
            'aktif',
        ])->name('aktif');

        Route::get('/', [
            SemesterController::class,
            'index',
        ])->name('index');

        Route::post('/', [
            SemesterController::class,
            'store',
        ])->name('store');

        Route::get('/{semester}', [
            SemesterController::class,
            'show',
        ])->name('show');

        Route::put('/{semester}', [
            SemesterController::class,
            'update',
        ])->name('update');

        Route::delete('/{semester}', [
            SemesterController::class,
            'destroy',
        ])->name('destroy');

        Route::post('/{semester}/aktifkan', [
            SemesterController::class,
            'aktifkan',
        ])->name('aktifkan');
    });


Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->get(
        '/akademik/tahun-ajaran/{tahunAjaran}/semester',
        [
            SemesterController::class,
            'byTahunAjaran',
        ]
    )
    ->name('akademik.tahun-ajaran.semester');


Route::middleware([
    'auth',
    'tenant',
    'subscription',
    'permission',
])
    ->get(
        '/akademik/tahun-ajaran/{tahunAjaran}/semester-aktif',
        [
            SemesterController::class,
            'aktifByTahunAjaran',
        ]
    )
    ->name('akademik.tahun-ajaran.semester-aktif');