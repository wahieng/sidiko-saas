<?php

namespace App\Core\Tenant\Models;

use App\Core\Identity\Models\User;
use App\Core\Subscription\Models\Langganan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'email',
        'phone',
        'address',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function langganan(): HasMany
    {
        return $this->hasMany(
            Langganan::class,
            'tenant_id'
        );
    }
}