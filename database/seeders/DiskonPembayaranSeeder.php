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
            ->whereHas('tahunAjaran', function ($query) use ($tenantId) {
                $query
                    ->where('tenant_id', $tenantId)
                    ->where('kode', '2026/2027');
            })
            ->where('status', 'AKTIF')
            ->with([
                'siswa',
                'kelompokRombel.rombel',
            ])
            ->get();

        if ($siswaTahun->isEmpty()) {
            return;
        }

        foreach ($siswaTahun as $item) {
            $siswa = $item->siswa;
            $kelompokRombel = $item->kelompokRombel;

            if (! $siswa || ! $kelompokRombel) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Pastikan relasi tetap satu tenant
            |--------------------------------------------------------------------------
            */
            if (
                $siswa->tenant_id !== $tenantId ||
                $kelompokRombel->tenant_id !== $tenantId
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Tarif SPP
            |--------------------------------------------------------------------------
            */
            $tarifSpp = TarifPembayaran::withoutGlobalScopes()
                ->where('tenant_id', $tenantId)
                ->where('kelompok_rombel_id', $kelompokRombel->id)
                ->where('aktif', true)
                ->whereHas('jenisPembayaran', function ($query) use ($tenantId) {
                    $query
                        ->where('tenant_id', $tenantId)
                        ->where('kode', 'SPP');
                })
                ->first();

            if (! $tarifSpp) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Contoh Diskon
            |--------------------------------------------------------------------------
            */
            $diskon = match ($siswa->nis) {
                '10001' => [
                    'tipe_diskon' => 'PERSEN',
                    'nilai' => 20,
                    'keterangan' => 'Diskon saudara kandung',
                ],

                '10002' => [
                    'tipe_diskon' => 'NOMINAL',
                    'nilai' => 50000,
                    'keterangan' => 'Diskon prestasi',
                ],

                default => null,
            };

            if (! $diskon) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan Diskon
            |--------------------------------------------------------------------------
            */
            DiskonPembayaran::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'siswa_tahun_id' => $item->id,
                        'tarif_pembayaran_id' => $tarifSpp->id,
                    ],
                    [
                        'tipe_diskon' => $diskon['tipe_diskon'],
                        'nilai' => $diskon['nilai'],
                        'keterangan' => $diskon['keterangan'],
                        'tanggal_mulai' => '2026-07-01',
                        'tanggal_selesai' => '2027-06-30',
                        'is_active' => true,
                    ]
                );
        }
    }
}