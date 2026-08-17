<?php

namespace App\Core\Tenant\Controllers;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Services\TenantService;
use Illuminate\Http\JsonResponse;
use App\Core\Tenant\Requests\StoreTenantRequest;
use App\Core\Tenant\Requests\UpdateTenantRequest;

class TenantController
{
    public function __construct(
        protected TenantService $tenantService
    ) {}

    /**
     * Display active tenants.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->tenantService->getActive()
        );
    }

    /**
     * Display a tenant.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json(
            $this->tenantService->find($id)
        );
    }

    /**
     * Store a new tenant.
     */
    public function store(StoreTenantRequest $request): JsonResponse
    {
        $tenant = $this->tenantService->create(
            $request->validated()
        );

        return response()->json($tenant, 201);
    }

    /**
     * Update a tenant.
     */
    public function update(
        UpdateTenantRequest $request,
        Tenant $tenant
    ): JsonResponse {
        $tenant = $this->tenantService->update(
            $tenant,
            $request->validated()
        );

        return response()->json($tenant);
    }

    /**
     * Activate tenant.
     */
    public function activate(Tenant $tenant): JsonResponse
    {
        return response()->json(
            $this->tenantService->activate($tenant)
        );
    }

    /**
     * Deactivate tenant.
     */
    public function deactivate(Tenant $tenant): JsonResponse
    {
        return response()->json(
            $this->tenantService->deactivate($tenant)
        );
    }
}