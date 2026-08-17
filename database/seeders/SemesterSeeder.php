<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::where(
            'kode',
            '2026/2027'
        )->firstOrFail();

        Semester::updateOrCreate(
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kode' => 'ganjil',
            ],
            [
                'nama' => 'Semester Ganjil',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-12-31',
                'aktif' => true,
            ]
        );

        Semester::updateOrCreate(
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'kode' => 'genap',
            ],
            [
                'nama' => 'Semester Genap',
                'tanggal_mulai' => '2027-01-01',
                'tanggal_selesai' => '2027-06-30',
                'aktif' => false,
            ]
        );
    }
}