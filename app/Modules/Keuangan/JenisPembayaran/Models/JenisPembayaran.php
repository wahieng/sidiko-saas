<?php

namespace App\Modules\Keuangan\JenisPembayaran\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;


class JenisPembayaran extends Model
{
    use BelongsToTenant;

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'tenant_id',
        'kode',
        'nama',
        'kategori',
        'keterangan',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}