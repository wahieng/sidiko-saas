<?php

namespace App\Modules\Siswa\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Siswa extends Model
{
    use BelongsToTenant;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nisn',
        'nik',
        'no_kk',
        'nama',
        'nama_panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'no_hp',
        'email',
        'alamat',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'anak_ke',
        'jumlah_saudara',
        'jenis_tinggal',
        'transportasi',
        'kebutuhan_khusus',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'anak_ke' => 'integer',
        'jumlah_saudara' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Riwayat siswa pada setiap tahun ajaran.
     */
    public function siswaTahun(): HasMany
    {
        return $this->hasMany(
            SiswaTahun::class,
            'siswa_id'
        );
    }

    /**
     * Semua tahun ajaran yang pernah diikuti siswa.
     */
    public function tahunAjaran(): HasManyThrough
    {
        return $this->hasManyThrough(
            TahunAjaran::class,
            SiswaTahun::class,
            'siswa_id',
            'id',
            'id',
            'tahun_ajaran_id'
        );
    }
}