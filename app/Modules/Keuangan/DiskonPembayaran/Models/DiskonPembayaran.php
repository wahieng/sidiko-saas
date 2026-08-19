<?php

namespace App\Modules\Keuangan\DiskonPembayaran\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\Siswa\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiskonPembayaran extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $table = 'diskon_pembayaran';

    protected $fillable = [
        'tenant_id',
        'siswa_id',
        'tarif_pembayaran_id',
        'tipe_diskon',
        'nilai',
        'keterangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'aktif' => 'boolean',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            Siswa::class,
            'siswa_id'
        );
    }

    public function tarifPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            TarifPembayaran::class,
            'tarif_pembayaran_id'
        );
    }
}