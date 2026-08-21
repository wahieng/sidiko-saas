<?php

namespace App\Modules\Keuangan\Tagihan\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    use BelongsToTenant;

    protected $table = 'tagihan';

    protected $fillable = [
        'tenant_id',
        'siswa_id',
        'jenis_pembayaran_id',
        'tarif_pembayaran_id',
        'diskon_pembayaran_id',
        'nomor_tagihan',
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
        'nominal',
        'diskon',
        'total',
        'dibayar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_tagihan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'nominal' => 'decimal:2',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'dibayar' => 'decimal:2',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            \App\Modules\Akademik\Siswa\Models\Siswa::class,
            'siswa_id'
        );
    }

    public function jenisPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            \App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran::class,
            'jenis_pembayaran_id'
        );
    }

    public function tarifPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            \App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran::class,
            'tarif_pembayaran_id'
        );
    }

    public function diskonPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            \App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran::class,
            'diskon_pembayaran_id'
        );
    }
}