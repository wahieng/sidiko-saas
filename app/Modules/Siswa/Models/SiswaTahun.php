<?php

namespace App\Modules\Siswa\Models;

use App\Modules\Akademik\Models\KelompokRombel;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaTahun extends Model
{
    use HasFactory;

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

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            Siswa::class,
            'siswa_id'
        );
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(
            TahunAjaran::class,
            'tahun_ajaran_id'
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