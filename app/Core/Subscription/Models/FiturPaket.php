<?php

namespace App\Core\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FiturPaket extends Model
{
    protected $table = 'fitur_paket';

    protected $fillable = [
        'paket_langganan_id',
        'kode_fitur',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function paket()
    {
        return $this->belongsTo(
            PaketLangganan::class,
            'paket_langganan_id'
        );
    }
}