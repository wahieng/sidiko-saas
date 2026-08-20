<?php

namespace App\Core\Billing\Models;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTagihanBilling extends Model
{
    use BelongsToTenant;

    protected $table = 'billing_detail_tagihan';

    protected $fillable = [
        'tenant_id',
        'billing_tagihan_id',
        'deskripsi',
        'qty',
        'harga',
        'subtotal',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'qty' => 'integer',
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Tenant pemilik detail tagihan.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(
            TagihanBilling::class,
            'billing_tagihan_id'
        );
    }
}