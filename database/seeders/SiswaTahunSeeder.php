<?php

namespace Database\Seeders;

use App\Modules\Akademik\KelompokRombel\Models\KelompokRombel;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use App\Modules\Siswa\Siswa\Models\Siswa;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Database\Seeder;

class SiswaTahunSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Tahun Ajaran
        |--------------------------------------------------------------------------
        */
        $tahunAjaran = TahunAjaran::withoutGlobalScopes()
            ->where('tenant_id', 1)
            ->where('kode', '2026/2027')
            ->firstOrFail();

        $tenantId = $tahunAjaran->tenant_id;

        /*
        |--------------------------------------------------------------------------
        | Kelompok Rombel VII A
        |--------------------------------------------------------------------------
        */
        $kelompokVIIA = KelompokRombel::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kode', 'A')
            ->whereHas('rombel', function ($query) use ($tenantId) {
                $query
                    ->withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('kode', 'VII');
            })
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Kelompok Rombel VII B
        |--------------------------------------------------------------------------
        */
        $kelompokVIIB = KelompokRombel::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kode', 'B')
            ->whereHas('rombel', function ($query) use ($tenantId) {
                $query
                    ->withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('kode', 'VII');
            })
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */
        $siswa1 = Siswa::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('nis', '10001')
            ->firstOrFail();

        $siswa2 = Siswa::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('nis', '10002')
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Siswa Tahun - Siswa 1
        |--------------------------------------------------------------------------
        */
        SiswaTahun::withoutGlobalScopes()
            ->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
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

        /*
        |--------------------------------------------------------------------------
        | Siswa Tahun - Siswa 2
        |--------------------------------------------------------------------------
        */
        SiswaTahun::withoutGlobalScopes()
            ->updateOrCreate(
                [
                    'tenant_id' => $tenantId,
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