<?php

namespace App\Core\Tenant\Traits;

use App\Core\Tenant\Context\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

trait BelongsToTenant
{
    /**
     * Boot trait.
     */
    protected static function bootBelongsToTenant(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Global Scope
        |--------------------------------------------------------------------------
        |
        | Model tenant-aware hanya boleh membaca data:
        |
        | 1. Tenant context aktif
        | 2. Atau user adalah superadmin/global
        |
        | Context kosong pada kondisi lain = DENY.
        |
        */

        static::addGlobalScope('tenant', function (
            Builder $builder
        ): void {
            $context = app(TenantContext::class);

            /*
            |--------------------------------------------------------------------------
            | Tenant context tersedia
            |--------------------------------------------------------------------------
            */

            if ($context->check()) {
                $builder->where(
                    $builder->getModel()->getTable() . '.tenant_id',
                    $context->id()
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Superadmin / global user
            |--------------------------------------------------------------------------
            |
            | Superadmin memiliki tenant_id = NULL.
            |
            | Mereka boleh mengakses lintas tenant.
            |--------------------------------------------------------------------------
            */

            $user = auth()->user();

            if ($user && $user->tenant_id === null) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Tidak ada context dan bukan user global.
            |--------------------------------------------------------------------------
            |
            | Jangan pernah membiarkan query tenant-aware berjalan
            | tanpa filter tenant.
            |--------------------------------------------------------------------------
            */

            $builder->whereRaw('1 = 0');
        });

        /*
        |--------------------------------------------------------------------------
        | Creating
        |--------------------------------------------------------------------------
        */

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);

            /*
            |--------------------------------------------------------------------------
            | Tenant context tersedia
            |--------------------------------------------------------------------------
            */

            if ($context->check()) {
                $model->tenant_id = $context->id();

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Superadmin
            |--------------------------------------------------------------------------
            |
            | Superadmin boleh membuat data tenant tertentu,
            | tetapi tenant_id WAJIB diberikan secara eksplisit.
            |--------------------------------------------------------------------------
            */

            $user = auth()->user();

            if ($user && $user->tenant_id === null) {
                if (! $model->tenant_id) {
                    throw new RuntimeException(
                        'Tenant ID wajib ditentukan saat membuat data tenant.'
                    );
                }

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | User biasa tanpa tenant context
            |--------------------------------------------------------------------------
            */

            throw new RuntimeException(
                'Tenant context belum tersedia saat membuat data tenant.'
            );
        });
    }

    /**
     * Relasi ke tenant.
     */
    public function tenant()
    {
        return $this->belongsTo(
            \App\Core\Tenant\Models\Tenant::class
        );
    }
}