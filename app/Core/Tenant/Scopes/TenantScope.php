<?php

namespace App\Core\Tenant\Scopes;

use App\Core\Tenant\Context\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(
        Builder $builder,
        Model $model
    ): void {
        $context = app(TenantContext::class);

        /*
        |--------------------------------------------------------------------------
        | Tenant Context tersedia
        |--------------------------------------------------------------------------
        */

        if ($context->has()) {
            $builder->where(
                $model->getTable() . '.tenant_id',
                $context->id()
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Superadmin / Global User
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();

        if ($user && $user->tenant_id === null) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Fail Closed
        |--------------------------------------------------------------------------
        */

        $builder->whereRaw('1 = 0');
    }
}