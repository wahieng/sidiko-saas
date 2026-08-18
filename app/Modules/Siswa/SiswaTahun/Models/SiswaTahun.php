<?php

namespace App\Modules\Siswa\SiswaTahun\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Akademik\KelompokRombel\Models\KelompokRombel;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use App\Modules\Siswa\Siswa\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaTahun extends Model
{
    use HasFactory;
    use BelongsToTenant;

    protected $table = 'siswa_tahun';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'kelompok_rombel_id',
        'status',
        'tanggal_masuk',
        'tanggal_keluar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            Siswa::class,
            'siswa_id'
        );
    }

    /**
     * Tahun ajaran.
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(
            TahunAjaran::class,
            'tahun_ajaran_id'
        );
    }

    /**
     * Kelompok rombel siswa.
     */
    public function kelompokRombel(): BelongsTo
    {
        return $this->belongsTo(
            KelompokRombel::class,
            'kelompok_rombel_id'
        );
    }
}