<?php

namespace App\Core\Billing\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatBilling extends Model
{
    use BelongsToTenant;

    protected $table = 'billing_riwayat';

    protected $fillable = [
        'tenant_id',
        'billing_tagihan_id',
        'billing_pembayaran_id',
        'aksi',
        'status_sebelumnya',
        'status_sesudahnya',
        'keterangan',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(
            TagihanBilling::class,
            'billing_tagihan_id'
        );
    }

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(
            PembayaranBilling::class,
            'billing_pembayaran_id'
        );
    }
}