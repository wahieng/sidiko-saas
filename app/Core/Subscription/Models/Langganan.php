<?php

namespace App\Core\Subscription\Models;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Langganan extends Model
{
    use BelongsToTenant;

    protected $table = 'langganan';

    protected $fillable = [
        'tenant_id',
        'paket_langganan_id',
        'status',
        'mulai_pada',
        'trial_berakhir_pada',
        'periode_mulai',
        'periode_berakhir',
        'dibatalkan_pada',
    ];

    protected $casts = [
        'mulai_pada' => 'datetime',
        'trial_berakhir_pada' => 'datetime',
        'periode_mulai' => 'date',
        'periode_berakhir' => 'date',
        'dibatalkan_pada' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(
            PaketLangganan::class,
            'paket_langganan_id'
        );
    }
}