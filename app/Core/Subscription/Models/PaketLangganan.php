<?php

namespace App\Core\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaketLangganan extends Model
{
    protected $table = 'paket_langganan';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'harga',
        'siklus_tagihan',
        'batas_siswa',
        'batas_pengguna',
        'batas_penyimpanan',
        'status',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'batas_siswa' => 'integer',
        'batas_pengguna' => 'integer',
        'batas_penyimpanan' => 'integer',
        'status' => 'boolean',
    ];

    public function fitur()
    {
        return $this->hasMany(
            FiturPaket::class,
            'paket_langganan_id'
        );
    }

    public function langganan()
    {
        return $this->hasMany(
            Langganan::class,
            'paket_langganan_id'
        );
    }
}