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

            /*
             * Jika tenant context tersedia,
             * otomatis isi tenant_id.
             */
            if ($context->check() && empty($model->tenant_id)) {
                $model->tenant_id = $context->require();
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