<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Akademik\Controllers\TahunAjaranController;
use App\Modules\Akademik\Controllers\RombelController;
use App\Modules\Akademik\Controllers\KelompokRombelController;

/*
|--------------------------------------------------------------------------
| Akademik Routes
|--------------------------------------------------------------------------
|
| Semua route yang berkaitan dengan pengelolaan akademik.
|
*/

Route::middleware(['auth'])->prefix('akademik')->name('akademik.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Tahun Ajaran
    |--------------------------------------------------------------------------
    */

    Route::get('/tahun-ajaran', [
        TahunAjaranController::class,
        'index',
    ])->name('tahun-ajaran.index');

    Route::post('/tahun-ajaran', [
        TahunAjaranController::class,
        'store',
    ])->name('tahun-ajaran.store');

    Route::get('/tahun-ajaran/{tahunAjaran}', [
        TahunAjaranController::class,
        'show',
    ])->name('tahun-ajaran.show');

    Route::put('/tahun-ajaran/{tahunAjaran}', [
        TahunAjaranController::class,
        'update',
    ])->name('tahun-ajaran.update');

    Route::delete('/tahun-ajaran/{tahunAjaran}', [
        TahunAjaranController::class,
        'destroy',
    ])->name('tahun-ajaran.destroy');


    /*
    |--------------------------------------------------------------------------
    | Rombel
    |--------------------------------------------------------------------------
    */

    Route::get('/rombel', [
        RombelController::class,
        'index',
    ])->name('rombel.index');

    Route::post('/rombel', [
        RombelController::class,
        'store',
    ])->name('rombel.store');

    Route::get('/rombel/{rombel}', [
        RombelController::class,
        'show',
    ])->name('rombel.show');

    Route::put('/rombel/{rombel}', [
        RombelController::class,
        'update',
    ])->name('rombel.update');

    Route::delete('/rombel/{rombel}', [
        RombelController::class,
        'destroy',
    ])->name('rombel.destroy');


    /*
    |--------------------------------------------------------------------------
    | Kelompok Rombel
    |--------------------------------------------------------------------------
    */

    Route::get('/kelompok-rombel', [
        KelompokRombelController::class,
        'index',
    ])->name('kelompok-rombel.index');

    Route::post('/kelompok-rombel', [
        KelompokRombelController::class,
        'store',
    ])->name('kelompok-rombel.store');

    Route::get('/kelompok-rombel/{kelompokRombel}', [
        KelompokRombelController::class,
        'show',
    ])->name('kelompok-rombel.show');

    Route::put('/kelompok-rombel/{kelompokRombel}', [
        KelompokRombelController::class,
        'update',
    ])->name('kelompok-rombel.update');

    Route::delete('/kelompok-rombel/{kelompokRombel}', [
        KelompokRombelController::class,
        'destroy',
    ])->name('kelompok-rombel.destroy');


    /*
    |--------------------------------------------------------------------------
    | Relasi / Data Pendukung
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk kebutuhan form dan AJAX.
    |
    */

    Route::get('/tahun-ajaran/{tahunAjaran}/semester', [
        TahunAjaranController::class,
        'semester',
    ])->name('tahun-ajaran.semester');

    Route::get('/tahun-ajaran/{tahunAjaran}/kelompok-rombel', [
        KelompokRombelController::class,
        'byTahunAjaran',
    ])->name('tahun-ajaran.kelompok-rombel');

    Route::get('/tahun-ajaran/{tahunAjaran}/rombel/{rombel}/kelompok', [
        KelompokRombelController::class,
        'byRombel',
    ])->name('tahun-ajaran.rombel.kelompok');
});