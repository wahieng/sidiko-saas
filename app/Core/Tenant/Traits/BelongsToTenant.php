<?php

namespace App\Core\Tenant\Traits;

use App\Core\Tenant\Context\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::creating(function (Model $model): void {
            $tenant = app(TenantContext::class)->require();

            if (empty($model->tenant_id)) {
                $model->tenant_id = $tenant->id;
            }
        });

        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenant = app(TenantContext::class)->get();

            if (! $tenant) {
                return;
            }

            $builder->where(
                $builder->getModel()->getTable() . '.tenant_id',
                $tenant->id
            );
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            \App\Core\Tenant\Models\Tenant::class
        );
    }
}