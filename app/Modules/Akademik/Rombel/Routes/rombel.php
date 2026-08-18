<?php

use App\Modules\Akademik\Rombel\Controllers\RombelController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('akademik/rombel')
    ->name('akademik.rombel.')
    ->group(function () {

        Route::get('/', [RombelController::class, 'index'])
            ->name('index');

        Route::post('/', [RombelController::class, 'store'])
            ->name('store');

        Route::get('/{rombel}', [RombelController::class, 'show'])
            ->name('show');

        Route::put('/{rombel}', [RombelController::class, 'update'])
            ->name('update');

        Route::delete('/{rombel}', [RombelController::class, 'destroy'])
            ->name('destroy');
    });