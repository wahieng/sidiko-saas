<?php

namespace App\Core\Tenant\Traits;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot trait.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(
            app(TenantScope::class)
        );

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);

            if ($context->has()) {
                if (empty($model->tenant_id)) {
                    $model->tenant_id = $context->require()->id;
                }

                return;
            }

            /*
             * Fail closed:
             *
             * Data tenant-owned tidak boleh dibuat tanpa
             * tenant context tersedia. Superadmin boleh bekerja
             * lintas tenant dengan diisi tenant_id eksplisit
             * atau dengan dileng context tenant aktif.
             */
            if (empty($model->tenant_id)) {
                abort(
                    403,
                    'Tenant context tidak tersedia untuk membuat data tenant-owned.'
                );
            }
        });
    }

    /**
     * Relasi model ke tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }
}