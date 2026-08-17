<?php

namespace App\Modules\Siswa\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nisn',
        'nik',
        'no_kk',
        'nama',
        'nama_panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'no_hp',
        'email',
        'alamat',
        'rt',
        'rw',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'anak_ke',
        'jumlah_saudara',
        'jenis_tinggal',
        'transportasi',
        'kebutuhan_khusus',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'anak_ke' => 'integer',
        'jumlah_saudara' => 'integer',
    ];
}