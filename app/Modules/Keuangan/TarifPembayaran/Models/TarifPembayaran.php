<?php

namespace App\Modules\Keuangan\TarifPembayaran\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran;
use App\Modules\Akademik\KelompokRombel\Models\KelompokRombel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TarifPembayaran extends Model
{
    use BelongsToTenant;

    protected $table = 'tarif_pembayaran';

    protected $fillable = [
        'tenant_id',
        'jenis_pembayaran_id',
        'kelompok_rombel_id',
        'nominal',
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'aktif' => 'boolean',
    ];

    public function jenisPembayaran(): BelongsTo
    {
        return $this->belongsTo(
            JenisPembayaran::class,
            'jenis_pembayaran_id'
        );
    }

    public function kelompokRombel(): BelongsTo
    {
        return $this->belongsTo(
            KelompokRombel::class,
            'kelompok_rombel_id'
        );
    }
}