<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Akademik\Models\KelompokRombel;
use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class KelompokRombelSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tahunAjaran = TahunAjaran::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('kode', '2026/2027')
            ->firstOrFail();

        $kelompok = [
            'VII' => ['A', 'B'],
            'VIII' => ['A', 'B'],
            'IX' => ['A', 'B'],
        ];

        foreach ($kelompok as $kodeRombel => $daftarKelompok) {
            $rombel = Rombel::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('kode', $kodeRombel)
                ->firstOrFail();

            foreach ($daftarKelompok as $index => $kodeKelompok) {
                KelompokRombel::withoutGlobalScopes()
                    ->updateOrCreate(
                        [
                            'tenant_id' => $tenant->id,
                            'tahun_ajaran_id' => $tahunAjaran->id,
                            'rombel_id' => $rombel->id,
                            'kode' => $kodeKelompok,
                        ],
                        [
                            'tenant_id' => $tenant->id,
                            'nama' => $rombel->nama . '-' . $kodeKelompok,
                            'urutan' => $index + 1,
                            'aktif' => true,
                        ]
                    );
            }
        }
    }
}