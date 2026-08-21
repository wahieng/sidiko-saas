<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Database\Seeder;

class DiskonPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantId = $tenant->id;

        /*
        |--------------------------------------------------------------------------
        | Siswa Tahun
        |--------------------------------------------------------------------------
        */

        $siswaTahun = SiswaTahun::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->get()
            ->keyBy('siswa_id');

        /*
        |--------------------------------------------------------------------------
        | Tarif Pembayaran
        |--------------------------------------------------------------------------
        */

        $tarifPembayaran = TarifPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->with('kelompokRombel.rombel')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Diskon Siswa 10001 - VII A
        |--------------------------------------------------------------------------
        */

        $siswaTahun1 = $siswaTahun->get(
            $this->getSiswaId($tenantId, '10001')
        );

        $tarifVIIA = $tarifPembayaran->first(
            fn ($tarif) =>
                $tarif->kelompokRombel?->rombel?->kode === 'VII'
                && $tarif->kelompokRombel?->kode === 'A'
        );

        if ($siswaTahun1 && $tarifVIIA) {
            DiskonPembayaran::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'siswa_tahun_id' => $siswaTahun1->id,
                        'tarif_pembayaran_id' => $tarifVIIA->id,
                    ],
                    [
                        'tipe_diskon' => 'PERSEN',
                        'nilai' => 10,
                        'tanggal_mulai' => '2026-07-01',
                        'tanggal_selesai' => '2027-06-30',
                        'is_active' => true,
                        'keterangan' => 'Diskon SPP siswa VII-A.',
                    ]
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Diskon Siswa 10002 - VII B
        |--------------------------------------------------------------------------
        */

        $siswaTahun2 = $siswaTahun->get(
            $this->getSiswaId($tenantId, '10002')
        );

        $tarifVIIB = $tarifPembayaran->first(
            fn ($tarif) =>
                $tarif->kelompokRombel?->rombel?->kode === 'VII'
                && $tarif->kelompokRombel?->kode === 'B'
        );

        if ($siswaTahun2 && $tarifVIIB) {
            DiskonPembayaran::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'siswa_tahun_id' => $siswaTahun2->id,
                        'tarif_pembayaran_id' => $tarifVIIB->id,
                    ],
                    [
                        'tipe_diskon' => 'PERSEN',
                        'nilai' => 10,
                        'tanggal_mulai' => '2026-07-01',
                        'tanggal_selesai' => '2027-06-30',
                        'is_active' => true,
                        'keterangan' => 'Diskon SPP siswa VII-B.',
                    ]
                );
        }
    }

    private function getSiswaId(
        int $tenantId,
        string $nis
    ): int {
        return \App\Modules\Siswa\Siswa\Models\Siswa::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('nis', $nis)
            ->valueOrFail('id');
    }
}