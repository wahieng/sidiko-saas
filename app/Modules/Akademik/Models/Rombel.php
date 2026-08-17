<?php

namespace App\Modules\Akademik\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    protected $table = 'rombel';

    protected $fillable = [
        'kode',
        'nama',
        'urutan',
        'aktif',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'aktif' => 'boolean',
    ];

    public function kelompokRombel(): HasMany
    {
        return $this->hasMany(
            KelompokRombel::class,
            'rombel_id'
        );
    }

    public function scopeAktif($query)
    {
        return $query
            ->where('aktif', true)
            ->orderBy('urutan');
    }
}