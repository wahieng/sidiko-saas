<?php

use App\Core\Tenant\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'tenant', 'permission'])
    ->prefix('tenants')
    ->name('core.tenant.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        Route::get('/', [TenantController::class, 'index'])
            ->name('index');

        Route::get('/{tenant}', [TenantController::class, 'show'])
            ->name('show');

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        Route::post('/', [TenantController::class, 'store'])
            ->name('store');

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        Route::put('/{tenant}', [TenantController::class, 'update'])
            ->name('update');

        Route::patch('/{tenant}/activate', [TenantController::class, 'activate'])
            ->name('activate');

        Route::patch('/{tenant}/deactivate', [TenantController::class, 'deactivate'])
            ->name('deactivate');
    });