<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Subscription Renewal
|--------------------------------------------------------------------------
|
| Membuat billing renewal untuk subscription
| yang sudah mendekati akhir periode.
|
*/

Schedule::command('subscription:generate-renewals')
    ->dailyAt('01:00')
    ->withoutOverlapping();