<?php

namespace App\Core\Access\Middleware;

use App\Core\Access\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function __construct(
        protected PermissionService $permissionService
    ) {
    }

    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $this->permissionService->has($user, $permission)) {
            abort(403, 'Anda tidak memiliki permission untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}