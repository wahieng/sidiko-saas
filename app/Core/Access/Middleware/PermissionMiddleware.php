<?php

namespace App\Core\Access\Middleware;

use App\Core\Access\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{   

    
    public function __construct(
        protected PermissionService $permissionService
    ) {
    }

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Automatic Permission
        |--------------------------------------------------------------------------
        */

        if (! config('permission.automatic', true)) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | HTTP Method → Action
        |--------------------------------------------------------------------------
        */

        $method = strtoupper($request->method());

        $action = config("permission.method_map.{$method}");

        if (! $action) {
            abort(
                403,
                'HTTP method belum memiliki aturan permission.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Route
        |--------------------------------------------------------------------------
        */

        $route = $request->route();

        if (! $route) {
            abort(403, 'Route tidak ditemukan.');
        }

        /*
        |--------------------------------------------------------------------------
        | Action Override
        |--------------------------------------------------------------------------
        |
        | Contoh:
        | POST /toggle-status
        |
        | Secara default POST = create.
        | Tetapi toggle-status sebenarnya update.
        |
        */

        $routeName = $route->getName();

        if ($routeName) {
            $overrides = config(
                'permission.action_overrides',
                []
            );

            foreach ($overrides as $pattern => $overrideAction) {
                if (Str::is($pattern, $routeName)) {
                    $action = $overrideAction;
                    break;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Resource
        |--------------------------------------------------------------------------
        */

        $resource = $this->resolveResource($request);

        if (! $resource) {
            abort(
                403,
                'Resource permission tidak dapat ditentukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Build Permission
        |--------------------------------------------------------------------------
        */

        $permission = "{$resource}.{$action}";


        
        /*
        |--------------------------------------------------------------------------
        | Check Permission
        |--------------------------------------------------------------------------
        */

        if (! $this->permissionService->has(
            $user,
            $permission
        )) {
            abort(
                403,
                'Anda tidak memiliki permission untuk mengakses halaman ini.'
            );
        }

        return $next($request);
    }

    /**
     * Resolve resource permission dari route name.
     *
     * Contoh:
     *
     * keuangan.diskon-pembayaran.index
     * keuangan.diskon-pembayaran.store
     * keuangan.diskon-pembayaran.show
     * keuangan.diskon-pembayaran.update
     * keuangan.diskon-pembayaran.destroy
     *
     * menjadi:
     *
     * diskon-pembayaran
     */
    protected function resolveResource(Request $request): ?string
    {
        $route = $request->route();

        if (! $route) {
            return null;
        }

        $routeName = $route->getName();

        if (! $routeName) {
            return null;
        }

        $segments = explode('.', $routeName);

        /*
        |--------------------------------------------------------------------------
        | Minimal format
        |--------------------------------------------------------------------------
        |
        | module.resource.action
        |
        | Contoh:
        |
        | siswa.dokumen.store
        | siswa.dokumen.index
        | keuangan.diskon-pembayaran.store
        |
        */

        if (count($segments) < 3) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Module + Resource
        |--------------------------------------------------------------------------
        */

        return $segments[count($segments) - 3]
            . '.'
            . $segments[count($segments) - 2];
    }
}