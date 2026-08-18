<?php

namespace App\Modules\Akademik\KelompokRombel\Models;

use App\Modules\Akademik\Rombel\Models\Rombel;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelompokRombel extends Model
{
    use BelongsToTenant;

    protected $table = 'kelompok_rombel';

    protected $fillable = [
        'tahun_ajaran_id',
        'rombel_id',
        'kode',
        'nama',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'tahun_ajaran_id' => 'integer',
        'rombel_id' => 'integer',
        'urutan' => 'integer',
        'aktif' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Tahun ajaran kelompok rombel.
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(
            TahunAjaran::class,
            'tahun_ajaran_id'
        );
    }

    /**
     * Rombel induk.
     */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(
            Rombel::class,
            'rombel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}