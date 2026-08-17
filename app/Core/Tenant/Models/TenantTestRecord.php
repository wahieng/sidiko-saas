<?php

namespace App\Core\Tenant\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantTestRecord extends Model
{
    use BelongsToTenant;

    protected $table = 'tenant_test_records';

    protected $fillable = [
        'tenant_id',
        'name',
    ];
}