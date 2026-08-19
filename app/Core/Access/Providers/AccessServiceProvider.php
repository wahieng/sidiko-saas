<?php

namespace App\Core\Access\Providers;

use Illuminate\Support\ServiceProvider;

class AccessServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            app_path('Core/Access/Config/permission.php'),
            'permission'
        );
    }

    public function boot(): void
    {
        //
    }
}