<?php

namespace App\Core\Tenant\Services;

use App\Core\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;

class TenantService
{
    /**
     * Get all active tenants.
     */
    public function getActive(): Collection
    {
        return Tenant::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Find tenant by ID.
     */
    public function find(int $id): Tenant
    {
        return Tenant::findOrFail($id);
    }

    /**
     * Create a new tenant.
     */
    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    /**
     * Update an existing tenant.
     */
    public function update(Tenant $tenant, array $data): Tenant
    {
        $tenant->update($data);

        return $tenant->fresh();
    }

    /**
     * Activate tenant.
     */
    public function activate(Tenant $tenant): Tenant
    {
        $tenant->update([
            'is_active' => true,
        ]);

        return $tenant->fresh();
    }

    /**
     * Deactivate tenant.
     */
    public function deactivate(Tenant $tenant): Tenant
    {
        $tenant->update([
            'is_active' => false,
        ]);

        return $tenant->fresh();
    }
}