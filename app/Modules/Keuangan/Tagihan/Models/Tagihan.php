<?php

namespace App\Modules\Keuangan\Tagihan\Models;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    use HasFactory;

    use BelongsToTenant;

    protected $table = 'tagihan';

    protected $fillable = [
        'tenant_id',
        'siswa_tahun_id',
        'tarif_pembayaran_id',
        'nomor_tagihan',
        'nominal_awal',
        'tipe_diskon',
        'nilai_diskon',
        'nominal_diskon',
        'nominal',
        'jumlah_dibayar',
        'sisa_tagihan',
        'tanggal_tagihan',
        'tanggal_jatuh_tempo',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'nominal_awal' => 'decimal:2',
        'nilai_diskon' => 'decimal:2',
        'nominal_diskon' => 'decimal:2',
        'nominal' => 'decimal:2',
        'jumlah_dibayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'tanggal_tagihan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    /**
     * Tenant pemilik tagihan.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }

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
     * Tarif yang menjadi sumber tagihan.
     */
    public function tarifPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            TarifPembayaran::class,
            'tarif_pembayaran_id'
        );
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(
            TagihanRiwayat::class,
            'tagihan_id'
        );
    }
}