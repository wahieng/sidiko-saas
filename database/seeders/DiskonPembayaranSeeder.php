<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\Siswa\Models\Siswa;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Database\Seeder;

class DiskonPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        /*
         * Ambil siswa yang memang sudah ditempatkan
         * pada tahun ajaran dan kelompok rombel.
         */
        $siswaTahun = SiswaTahun::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->whereHas('tahunAjaran', function ($query) {
                $query->where('kode', '2026/2027');
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

            if (!$siswa || !$kelompokRombel) {
                continue;
            }

            /*
             * Cari tarif pembayaran berdasarkan
             * kelompok rombel siswa.
             *
             * Tarif tidak memiliki kolom kode.
             * Jenis pembayaran SPP berada di relasi tarif.
             */
            $tarifSpp = TarifPembayaran::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('kelompok_rombel_id', $kelompokRombel->id)
                ->where('aktif', true)
                ->whereHas('jenisPembayaran', function ($query) {
                    $query->where('kode', 'SPP');
                })
                ->first();

            if (!$tarifSpp) {
                continue;
            }

            /*
             * Data contoh diskon berdasarkan siswa.
             *
             * Siswa 10001 → diskon saudara kandung 20%
             * Siswa 10002 → diskon prestasi 25%
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

            if (!$diskon) {
                continue;
            }

            DiskonPembayaran::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'siswa_id' => $siswa->id,
                        'tarif_pembayaran_id' => $tarifSpp->id,
                    ],
                    [
                        'tipe_diskon' => $diskon['tipe_diskon'],
                        'nilai' => $diskon['nilai'],
                        'keterangan' => $diskon['keterangan'],
                        'tanggal_mulai' => '2026-07-01',
                        'tanggal_selesai' => '2027-06-30',
                        'aktif' => true,
                    ]
                );
        }
    }
}