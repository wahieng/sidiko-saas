<?php

namespace App\Core\TransactionNumber\Providers;

use Illuminate\Support\ServiceProvider;

class TransactionNumberServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            app_path('Core/TransactionNumber/Config/transaction_number.php'),
            'transaction_number'
        );
    }

    public function boot(): void
    {
        //
    }
}