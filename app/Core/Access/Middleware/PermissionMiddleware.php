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
        | HTTP Method → Permission Action
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
            abort(
                403,
                'Route tidak ditemukan.'
            );
        }

        $routeName = $route->getName();

        if (! $routeName) {
            abort(
                403,
                'Route tidak memiliki nama.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Action Override
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk route yang HTTP method-nya tidak sesuai
        | dengan aksi permission sebenarnya.
        |
        | Contoh:
        |
        | POST tenant.activate
        |
        | Secara default:
        | POST = create
        |
        | Tetapi activate adalah update.
        |
        */

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

        /*
        |--------------------------------------------------------------------------
        | Resolve Resource
        |--------------------------------------------------------------------------
        |
        | Format route:
        |
        | module.resource.action
        |
        | Contoh:
        |
        | akademik.semester.index
        | akademik.semester.store
        | keuangan.tarif-pembayaran.index
        | siswa.siswa.show
        |
        */

        $resource = $this->resolveResource($routeName);

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
     * Resolve resource dari nama route.
     *
     * Format:
     *
     * module.resource.action
     *
     * Contoh:
     *
     * akademik.semester.index
     *       ↓
     * akademik.semester
     *
     * keuangan.tarif-pembayaran.store
     *       ↓
     * keuangan.tarif-pembayaran
     */
    protected function resolveResource(
        string $routeName
    ): ?string {
        $segments = explode('.', $routeName);

        /*
        |--------------------------------------------------------------------------
        | Minimal route:
        |
        | module.resource.action
        |--------------------------------------------------------------------------
        */

        if (count($segments) < 3) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Action adalah segment terakhir.
        |
        | Resource adalah segment sebelum action.
        |
        | Module adalah segment sebelum resource.
        |--------------------------------------------------------------------------
        */

        $action = array_pop($segments);
        $resource = array_pop($segments);

        if (! $action || ! $resource) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil module terakhir.
        |
        | Ini memungkinkan route bertingkat seperti:
        |
        | akademik.master.semester.index
        |
        | tetap menghasilkan:
        |
        | master.semester
        |
        | Namun untuk SIDIKO sebaiknya tetap menggunakan
        | module.resource.action.
        |--------------------------------------------------------------------------
        */

        $module = array_pop($segments);

        if (! $module) {
            return null;
        }

        return "{$module}.{$resource}";
    }
}