<?php

namespace App\Modules\Akademik\TahunAjaran\Models;

use App\Core\Tenant\Scopes\TenantScope;
use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Akademik\KelompokRombel\Models\KelompokRombel;
use App\Modules\Akademik\Semester\Models\Semester;
use App\Modules\Siswa\Models\SiswaTahun;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    use BelongsToTenant;

    protected $table = 'tahun_ajaran';

    /**
     * Tenant scope.
     *
     * Semua query Tahun Ajaran otomatis
     * dibatasi berdasarkan tenant aktif.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    protected $fillable = [
        'tenant_id',
        'kode',
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
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
            SiswaTahun::class,
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
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }
}