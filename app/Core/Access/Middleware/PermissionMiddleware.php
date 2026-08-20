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
        | Format standar SIDIKO:
        |
        | module.resource.action
        |
        | Contoh:
        |
        | core.tenant.index
        | akademik.semester.index
        | keuangan.jenis-pembayaran.index
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
     * Resolve resource dari route name.
     *
     * Format:
     *
     * module.resource.action
     *
     * Contoh:
     *
     * core.tenant.index
     *       ↓
     * core.tenant
     *
     * akademik.semester.index
     *       ↓
     * akademik.semester
     */
    protected function resolveResource(
        string $routeName
    ): ?string {
        $segments = explode('.', $routeName);

        if (count($segments) !== 3) {
            return null;
        }

        [$module, $resource, $routeAction] = $segments;

        if (
            $module === ''
            || $resource === ''
            || $routeAction === ''
        ) {
            return null;
        }

        return "{$module}.{$resource}";
    }
}