<?php

namespace App\Core\Subscription\Middleware;

use App\Core\Subscription\Models\Langganan;
use App\Core\Tenant\Context\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        if (! $user) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Superadmin
        |--------------------------------------------------------------------------
        |
        | Superadmin tidak terikat subscription tenant.
        |
        */

        if ($user->tenant_id === null) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Tenant
        |--------------------------------------------------------------------------
        */

        $tenant = $this->tenantContext->get();

        if (! $tenant) {
            abort(403, 'Tenant context tidak tersedia.');
        }

        /*
        |--------------------------------------------------------------------------
        | Subscription
        |--------------------------------------------------------------------------
        */

        $subscription = Langganan::query()
            ->where('tenant_id', $tenant->id)
            ->latest('id')
            ->first();

        $status = $subscription?->status
            ?? config('subscription.default_status', 'active');

        /*
        |--------------------------------------------------------------------------
        | Access Rule
        |--------------------------------------------------------------------------
        */

        $rule = config("subscription.access.{$status}");

        if (! $rule) {
            abort(
                403,
                'Status subscription tidak memiliki aturan akses.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Full Access
        |--------------------------------------------------------------------------
        */

        if (($rule['mode'] ?? 'blocked') === 'full') {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Restricted / Blocked Access
        |--------------------------------------------------------------------------
        */

        $routeName = $request->route()?->getName();

        if (! $routeName) {
            abort(
                403,
                'Route subscription tidak ditemukan.'
            );
        }

        $allowedRoutes = $rule['allowed_routes'] ?? [];

        foreach ($allowedRoutes as $pattern) {

            if (
                $pattern === '*'
                || Str::is($pattern, $routeName)
            ) {
                return $next($request);
            }
        }

        abort(
            403,
            'Subscription tidak aktif untuk mengakses fitur ini.'
        );
    }
}