<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\KelompokRombel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelompokRombelSeeder extends Seeder
{
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::where('kode', '2026/2027')->firstOrFail();

        $kelompok = [
            'VII' => ['A', 'B'],
            'VIII' => ['A', 'B'],
            'IX' => ['A', 'B'],
        ];

        foreach ($kelompok as $kodeRombel => $daftarKelompok) {
            $rombel = Rombel::where('kode', $kodeRombel)
                ->firstOrFail();

            foreach ($daftarKelompok as $index => $kodeKelompok) {
                KelompokRombel::updateOrCreate(
                    [
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'rombel_id' => $rombel->id,
                        'kode' => $kodeKelompok,
                    ],
                    [
                        'nama' => $rombel->nama . '-' . $kodeKelompok,
                        'urutan' => $index + 1,
                        'aktif' => true,
                    ]
                );
            }
        }
    }
}