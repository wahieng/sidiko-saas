<?php

namespace App\Modules\Siswa\Models;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wali extends Model
{
    use BelongsToTenant;

    protected $table = 'wali';

    protected $fillable = [
        'siswa_id',
        'nama',
        'nik',
        'hubungan',
        'tempat_lahir',
        'tanggal_lahir',
        'pendidikan',
        'pekerjaan',
        'penghasilan',
        'no_hp',
        'email',
        'alamat',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'penghasilan' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Tenant pemilik data wali.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }

    /**
     * Siswa yang memiliki wali ini.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(
            Siswa::class,
            'siswa_id'
        );
    }
}