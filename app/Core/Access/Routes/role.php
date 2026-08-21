<?php

use App\Core\Access\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROLE
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'tenant',
    'permission',
])
    ->prefix('roles')
    ->name('access.role.')
    ->group(function () {

        Route::get('/', [
            RoleController::class,
            'index',
        ])->name('index');

        Route::get('/{role}', [
            RoleController::class,
            'show',
        ])->name('show');

        Route::post('/', [
            RoleController::class,
            'store',
        ])->name('store');

        Route::put('/{role}', [
            RoleController::class,
            'update',
        ])->name('update');

        Route::patch('/{role}/activate', [
            RoleController::class,
            'activate',
        ])->name('activate');

        Route::patch('/{role}/deactivate', [
            RoleController::class,
            'deactivate',
        ])->name('deactivate');
    });