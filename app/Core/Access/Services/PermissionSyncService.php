<?php

namespace App\Core\Access\Services;

use App\Core\Access\Models\Permission;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class PermissionSyncService
{
    public function sync(): array
    {
        $routesScanned = 0;
        $permissionRoutes = 0;
        $permissionsFound = 0;
        $created = 0;
        $existing = 0;
        $invalid = 0;
        $invalidRoutes = [];

        foreach (RouteFacade::getRoutes() as $route) {
            $routesScanned++;

            if (! $this->usesPermissionMiddleware($route)) {
                continue;
            }

            $permissionRoutes++;

            $permission = $this->resolvePermission($route);

            if (! $permission) {
                $invalid++;

                $invalidRoutes[] = [
                    'methods' => collect($route->methods())
                        ->reject(fn ($method) => $method === 'HEAD')
                        ->implode('|'),

                    'uri' => $route->uri(),

                    'name' => $route->getName() ?? '[NO NAME]',
                ];

                continue;
            }

            $permissionsFound++;

            $model = Permission::updateOrCreate(
                [
                    'code' => $permission['code'],
                ],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'description' => $permission['description'],
                    'is_active' => true,
                ]
            );

            if ($model->wasRecentlyCreated) {
                $created++;
            } else {
                $existing++;
            }
        }

        return [
            'routes_scanned' => $routesScanned,
            'permission_routes' => $permissionRoutes,
            'permissions_found' => $permissionsFound,
            'created' => $created,
            'existing' => $existing,
            'invalid' => $invalid,
            'invalid_routes' => $invalidRoutes,
        ];
    }

    /**
     * Resolve permission berdasarkan:
     *
     * route:
     * module.resource.action
     *
     * permission:
     * module.resource.permission_action
     */
    protected function resolvePermission(Route $route): ?array
    {
        $routeName = $route->getName();

        if (! $routeName) {
            return null;
        }

        $segments = explode('.', $routeName);

        /*
        |--------------------------------------------------------------------------
        | Format route SIDIKO wajib:
        |
        | module.resource.action
        |
        | Contoh:
        |
        | akademik.semester.index
        | keuangan.tagihan.store
        | core.billing.generate
        |
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Resolve HTTP method
        |--------------------------------------------------------------------------
        |
        | GET     => view
        | POST    => create
        | PUT     => update
        | PATCH   => update
        | DELETE  => delete
        |
        */

        $methods = collect($route->methods())
            ->map(fn ($method) => strtoupper($method))
            ->reject(fn ($method) => $method === 'HEAD')
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Route harus memiliki tepat satu method permission.
        |--------------------------------------------------------------------------
        */

        if ($methods->count() !== 1) {
            return null;
        }

        $method = $methods->first();

        $action = config("permission.method_map.{$method}");

        if (! $action) {
            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan action memang terdaftar di config.
        |--------------------------------------------------------------------------
        */

        if (! in_array(
            $action,
            config('permission.actions', []),
            true
        )) {
            return null;
        }

        $code = "{$module}.{$resource}.{$action}";

        return [
            'code' => $code,

            'module' => $module,

            'name' => $this->makeName(
                $module,
                $resource,
                $action
            ),

            'description' =>
                "Permission {$action} untuk {$module}.{$resource}.",
        ];
    }

    /**
     * Cek apakah route menggunakan PermissionMiddleware.
     */
    protected function usesPermissionMiddleware(Route $route): bool
    {
        return collect($route->gatherMiddleware())
            ->contains(
                function ($middleware) {
                    return $middleware === 'permission'
                        || str_ends_with(
                            $middleware,
                            '\\PermissionMiddleware'
                        );
                }
            );
    }

    /**
     * Generate nama permission.
     */
    protected function makeName(
        string $module,
        string $resource,
        string $action
    ): string {
        return ucfirst($action)
            . ' '
            . ucfirst(
                str_replace(
                    ['-', '_'],
                    ' ',
                    $resource
                )
            )
            . ' '
            . ucfirst($module);
    }
}