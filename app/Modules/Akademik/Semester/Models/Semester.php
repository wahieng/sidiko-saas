<?php

namespace App\Modules\Akademik\Semester\Models;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Traits\BelongsToTenant;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    use BelongsToTenant;

    protected $table = 'semester';

    protected $fillable = [
        'tenant_id',
        'tahun_ajaran_id',
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

    /**
     * Tenant pemilik semester.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            Tenant::class,
            'tenant_id'
        );
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(
            TahunAjaran::class,
            'tahun_ajaran_id'
        );
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}