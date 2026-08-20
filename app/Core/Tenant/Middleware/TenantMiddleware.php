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
        /*
        |--------------------------------------------------------------------------
        | Pastikan context bersih sebelum request diproses.
        |--------------------------------------------------------------------------
        */

        $this->tenantContext->clear();

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | User belum login.
        |
        | Middleware auth biasanya menangani ini.
        |--------------------------------------------------------------------------
        */

        if (! $user) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Superadmin / user global.
        |
        | tenant_id = NULL berarti tidak terikat
        | pada tenant tertentu.
        |--------------------------------------------------------------------------
        */

        if ($user->tenant_id === null) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Set tenant dari user.
        |--------------------------------------------------------------------------
        */

        $this->tenantContext->setFromUser($user);

        /*
        |--------------------------------------------------------------------------
        | Tenant tidak ditemukan atau tidak aktif.
        |--------------------------------------------------------------------------
        */

        if (! $this->tenantContext->check()) {
            abort(
                403,
                'Tenant tidak ditemukan atau tidak aktif.'
            );
        }

        return $next($request);
    }
}