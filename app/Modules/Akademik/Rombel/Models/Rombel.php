<?php

namespace App\Modules\Akademik\Rombel\Models;

use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rombel extends Model
{
    use BelongsToTenant;

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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Kelompok rombel.
     */
    public function kelompokRombel(): HasMany
    {
        return $this->hasMany(
            KelompokRombel::class,
            'rombel_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Rombel aktif berdasarkan urutan.
     */
    public function scopeAktif($query)
    {
        return $query
            ->where('aktif', true)
            ->orderBy('urutan');
    }
}