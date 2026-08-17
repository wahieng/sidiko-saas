<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\KelompokRombel;
use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Siswa\Models\SiswaTahun;
use Illuminate\Database\Seeder;

class SiswaTahunSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::where(
            'kode',
            '2026/2027'
        )->firstOrFail();

        $kelompokVIIA = KelompokRombel::where(
            'tahun_ajaran_id',
            $tahunAjaran->id
        )
            ->where('kode', 'A')
            ->whereHas('rombel', function ($query) {
                $query->where('kode', 'VII');
            })
            ->firstOrFail();

        $kelompokVIIB = KelompokRombel::where(
            'tahun_ajaran_id',
            $tahunAjaran->id
        )
            ->where('kode', 'B')
            ->whereHas('rombel', function ($query) {
                $query->where('kode', 'VII');
            })
            ->firstOrFail();

        $siswa1 = Siswa::where('nis', '10001')->firstOrFail();
        $siswa2 = Siswa::where('nis', '10002')->firstOrFail();

        SiswaTahun::updateOrCreate(
            [
                'siswa_id' => $siswa1->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ],
            [
                'kelompok_rombel_id' => $kelompokVIIA->id,
                'status' => 'AKTIF',
                'tanggal_masuk' => '2026-07-01',
                'tanggal_keluar' => null,
                'keterangan' => null,
            ]
        );

        SiswaTahun::updateOrCreate(
            [
                'siswa_id' => $siswa2->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
            ],
            [
                'kelompok_rombel_id' => $kelompokVIIB->id,
                'status' => 'AKTIF',
                'tanggal_masuk' => '2026-07-01',
                'tanggal_keluar' => null,
                'keterangan' => null,
            ]
        );
    }
}