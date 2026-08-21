<?php

namespace App\Modules\Keuangan\Tagihan\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanRiwayat extends Model
{
    use BelongsToTenant;

    protected $table = 'tagihan_riwayat';

    protected $fillable = [
        'tenant_id',
        'tagihan_id',
        'aksi',
        'data_sebelum',
        'data_sesudah',
        'keterangan',
    ];

    protected $casts = [
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(
            Tagihan::class,
            'tagihan_id'
        );
    }
}