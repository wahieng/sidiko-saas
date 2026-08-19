<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Akademik\KelompokRombel\Models\KelompokRombel;
use App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TarifPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $jenisPembayaran = JenisPembayaran::query()
            ->where('tenant_id', $tenant->id)
            ->get()
            ->keyBy('kode');

        $kelompokRombel = KelompokRombel::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->where('aktif', true)
            ->with('rombel')
            ->get();

        $data = [
            [
                'jenis_kode' => 'SPP',
                'rombel_kode' => 'VII',
                'kelompok_kode' => 'A',
                'nominal' => 150000,
                'keterangan' => 'Tarif SPP VII-A.',
            ],
            [
                'jenis_kode' => 'SPP',
                'rombel_kode' => 'VII',
                'kelompok_kode' => 'B',
                'nominal' => 150000,
                'keterangan' => 'Tarif SPP VII-B.',
            ],
            [
                'jenis_kode' => 'SPP',
                'rombel_kode' => 'VIII',
                'kelompok_kode' => 'A',
                'nominal' => 175000,
                'keterangan' => 'Tarif SPP VIII-A.',
            ],
            [
                'jenis_kode' => 'SPP',
                'rombel_kode' => 'VIII',
                'kelompok_kode' => 'B',
                'nominal' => 175000,
                'keterangan' => 'Tarif SPP VIII-B.',
            ],
            [
                'jenis_kode' => 'SPP',
                'rombel_kode' => 'IX',
                'kelompok_kode' => 'A',
                'nominal' => 200000,
                'keterangan' => 'Tarif SPP IX-A.',
            ],
            [
                'jenis_kode' => 'SPP',
                'rombel_kode' => 'IX',
                'kelompok_kode' => 'B',
                'nominal' => 200000,
                'keterangan' => 'Tarif SPP IX-B.',
            ],
        ];

        foreach ($data as $item) {
            $jenis = $jenisPembayaran->get($item['jenis_kode']);

            $kelompok = $kelompokRombel->first(
                fn ($itemKelompok) =>
                    $itemKelompok->rombel?->kode === $item['rombel_kode']
                    && $itemKelompok->kode === $item['kelompok_kode']
            );

            if (!$jenis || !$kelompok) {
                continue;
            }

            DB::table('tarif_pembayaran')->updateOrInsert(
                [
                    'tenant_id' => $tenant->id,
                    'jenis_pembayaran_id' => $jenis->id,
                    'kelompok_rombel_id' => $kelompok->id,
                ],
                [
                    'nominal' => $item['nominal'],
                    'keterangan' => $item['keterangan'],
                    'aktif' => true,
                    'updated_at' => now(),
                ]
            );
        }
    }
}