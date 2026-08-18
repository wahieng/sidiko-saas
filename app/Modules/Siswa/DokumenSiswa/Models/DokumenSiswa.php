<?php

namespace App\Modules\Siswa\DokumenSiswa\Models;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Siswa\Siswa\Models\Siswa;

class DokumenSiswa extends Model
{
    use BelongsToTenant;

    protected $table = 'dokumen_siswa';

    protected $fillable = [
        'siswa_id',
        'jenis_dokumen',
        'nama_file',
        'nama_asli',
        'path',
        'disk',
        'mime_type',
        'ukuran',
        'keterangan',
    ];

    protected $casts = [
        'siswa_id' => 'integer',
        'ukuran' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Tenant pemilik dokumen.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }

    /**
     * Siswa pemilik dokumen.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            Siswa::class,
            'siswa_id'
        );
    }
}