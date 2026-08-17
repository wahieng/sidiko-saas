<?php

namespace App\Core\Tenant\Middleware;

use App\Core\Tenant\Context\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
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

        if (! $user) {
            return $next($request);
        }

        // Superadmin tidak otomatis terikat pada tenant.
        if ($user->tenant_id === null) {
            return $next($request);
        }

        $this->tenantContext->setFromUser($user);

        return $next($request);
    }
}