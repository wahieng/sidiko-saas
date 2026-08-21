<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenantServiceProvider::class,
    App\Core\Identity\Providers\IdentityServiceProvider::class,
    App\Core\Subscription\Providers\SubscriptionServiceProvider::class,
    App\Core\Access\Providers\AccessServiceProvider::class,
    App\Core\TransactionNumber\Providers\TransactionNumberServiceProvider::class,
];
