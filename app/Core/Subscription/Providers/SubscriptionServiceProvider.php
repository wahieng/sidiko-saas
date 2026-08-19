<?php

namespace App\Core\Subscription\Providers;

use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            app_path('Core/Subscription/Config/subscription.php'),
            'subscription'
        );
    }

    public function boot(): void
    {
        //
    }
}