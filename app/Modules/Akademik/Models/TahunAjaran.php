<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
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

    public function semesters(): HasMany
    {
        return $this->hasMany(
            Semester::class,
            'tahun_ajaran_id'
        );
    }

    public function kelompokRombel(): HasMany
    {
        return $this->hasMany(
            KelompokRombel::class,
            'tahun_ajaran_id'
        );
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}