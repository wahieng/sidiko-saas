<?php

namespace App\Core\Billing\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PembayaranBilling extends Model
{
    use BelongsToTenant;

    protected $table = 'billing_pembayaran';

    protected $fillable = [
        'tenant_id',
        'billing_tagihan_id',
        'nomor_pembayaran',
        'tanggal_pembayaran',
        'jumlah',
        'metode',
        'referensi',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(
            TagihanBilling::class,
            'billing_tagihan_id'
        );
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(
            RiwayatBilling::class,
            'billing_pembayaran_id'
        );
    }
}