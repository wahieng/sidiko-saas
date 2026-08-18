<?php

namespace App\Modules\Akademik\TahunAjaran\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Akademik\Semester\Models\Semester;

class TahunAjaran extends Model
{
    use BelongsToTenant;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'kode',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'aktif' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Semester dalam tahun ajaran.
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(
            Semester::class,
            'tahun_ajaran_id'
        );
    }

    /**
     * Kelompok rombel dalam tahun ajaran.
     */
    public function kelompokRombel(): HasMany
    {
        return $this->hasMany(
            KelompokRombel::class,
            'tahun_ajaran_id'
        );
    }

    /**
     * Riwayat siswa pada tahun ajaran.
     */
    public function siswaTahun(): HasMany
    {
        return $this->hasMany(
            \App\Modules\Siswa\Models\SiswaTahun::class,
            'tahun_ajaran_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Hanya tahun ajaran aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}