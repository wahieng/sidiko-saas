<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        TahunAjaran::updateOrCreate(
            [
                'kode' => '2026/2027',
            ],
            [
                'nama' => 'Tahun Ajaran 2026/2027',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
                'aktif' => true,
            ]
        );
    }
}