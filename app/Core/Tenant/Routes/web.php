<?php

use App\Core\Tenant\Controllers\TenantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:tenant.view'])
    ->prefix('tenants')
    ->name('tenants.')
    ->group(function () {

        Route::get('/', [TenantController::class, 'index'])
            ->name('index');

        Route::get('/{id}', [TenantController::class, 'show'])
            ->name('show');
    });

Route::middleware(['auth', 'permission:tenant.create'])
    ->prefix('tenants')
    ->name('tenants.')
    ->group(function () {

        Route::post('/', [TenantController::class, 'store'])
            ->name('store');
    });

Route::middleware(['auth', 'permission:tenant.update'])
    ->prefix('tenants')
    ->name('tenants.')
    ->group(function () {

        Route::put('/{tenant}', [TenantController::class, 'update'])
            ->name('update');

        Route::patch('/{tenant}/activate', [TenantController::class, 'activate'])
            ->name('activate');

        Route::patch('/{tenant}/deactivate', [TenantController::class, 'deactivate'])
            ->name('deactivate');
    });