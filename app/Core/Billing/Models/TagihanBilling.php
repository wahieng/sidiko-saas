<?php

namespace App\Core\Billing\Models;

use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Tenant\Scopes\TenantScope;
use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagihanBilling extends Model
{
    use BelongsToTenant;

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    protected $table = 'billing_tagihan';

    protected $fillable = [
        'tenant_id',
        'langganan_id',
        'paket_langganan_id',
        'nomor_tagihan',
        'tanggal_tagihan',
        'jatuh_tempo',
        'periode_mulai',
        'periode_berakhir',
        'subtotal',
        'diskon',
        'total',
        'status',
        'dibayar_pada',
        'catatan',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'jatuh_tempo' => 'date',
        'periode_mulai' => 'date',
        'periode_berakhir' => 'date',
        'subtotal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'dibayar_pada' => 'datetime',
    ];

    public function langganan(): BelongsTo
    {
        return $this->belongsTo(
            Langganan::class,
            'langganan_id'
        );
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(
            PaketLangganan::class,
            'paket_langganan_id'
        );
    }

    public function detail(): HasMany
    {
        return $this->hasMany(
            DetailTagihanBilling::class,
            'billing_tagihan_id'
        );
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(
            PembayaranBilling::class,
            'billing_tagihan_id'
        );
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(
            RiwayatBilling::class,
            'billing_tagihan_id'
        );
    }
}