<?php

namespace App\Core\Tenant\Traits;

use App\Core\Tenant\Models\Tenant;

trait BelongsToTenant
{
    /**
     * Relasi model ke tenant.
     */
    public function tenant()
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }
}