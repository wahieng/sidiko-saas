<?php

namespace App\Core\Access\Controllers;

use App\Core\Access\Models\Role;
use App\Core\Access\Requests\StoreRoleRequest;
use App\Core\Access\Requests\UpdateRoleRequest;
use App\Core\Access\Services\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController
{
    public function __construct(
        protected RoleService $service
    ) {
    }

    /**
     * Get all active roles.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->getActive()
        );
    }

    /**
     * Show role by ID.
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json(
            $this->service->find($role->id)
        );
    }

    /**
     * Create a new role.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->service->create(
            $request->validated()
        );

        return response()->json($role, 201);
    }

    /**
     * Update an existing role.
     */
    public function update(
        UpdateRoleRequest $request,
        Role $role
    ): JsonResponse {
        $role = $this->service->update(
            $role,
            $request->validated()
        );

        return response()->json($role);
    }

    /**
     * Activate role.
     */
    public function activate(Role $role): JsonResponse
    {
        $role = $this->service->activate($role);

        return response()->json($role);
    }

    /**
     * Deactivate role.
     */
    public function deactivate(Role $role): JsonResponse
    {
        $role = $this->service->deactivate($role);

        return response()->json($role);
    }
}