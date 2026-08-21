<?php

namespace App\Modules\Keuangan\DiskonPembayaran\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiskonPembayaran extends Model
{
    use BelongsToTenant;

    protected $table = 'diskon_pembayaran';

    protected $fillable = [
        'tenant_id',
        'siswa_tahun_id',
        'tarif_pembayaran_id',
        'tipe_diskon',
        'nilai',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Siswa dalam konteks tahun ajaran.
     */
    public function siswaTahun(): BelongsTo
    {
        return $this->belongsTo(
            SiswaTahun::class,
            'siswa_tahun_id'
        );
    }

    /**
     * Tarif pembayaran yang mendapatkan diskon.
     */
    public function tarifPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            TarifPembayaran::class,
            'tarif_pembayaran_id'
        );
    }
}