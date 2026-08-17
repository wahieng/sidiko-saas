<?php

use App\Core\Subscription\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('subscription')->name('subscription.')->group(function () {

    Route::get('/', [
        SubscriptionController::class,
        'index',
    ])->name('index');

    Route::get('/{langganan}', [
        SubscriptionController::class,
        'show',
    ])->name('show');

    Route::post('/trial', [
        SubscriptionController::class,
        'buatTrial',
    ])->name('trial');

    Route::post('/{langganan}/aktifkan', [
        SubscriptionController::class,
        'aktifkan',
    ])->name('aktifkan');

    Route::post('/{langganan}/batalkan', [
        SubscriptionController::class,
        'batalkan',
    ])->name('batalkan');

    Route::post('/{langganan}/suspend', [
        SubscriptionController::class,
        'suspend',
    ])->name('suspend');

    Route::post('/{langganan}/expire', [
        SubscriptionController::class,
        'expire',
    ])->name('expire');
});