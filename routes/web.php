<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Core Modules
|--------------------------------------------------------------------------
*/

require base_path('app/Core/Identity/Routes/web.php');
require base_path('app/Core/Identity/Routes/auth.php');

require base_path('app/Core/Tenant/Routes/web.php');

require base_path('app/Core/Subscription/Routes/subscription.php');

require base_path('app/Core/Billing/Routes/billing.php');