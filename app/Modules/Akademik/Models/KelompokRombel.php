<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KelompokRombel extends Model
{
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

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(
            TahunAjaran::class,
            'tahun_ajaran_id'
        );
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(
            Rombel::class,
            'rombel_id'
        );
    }
}