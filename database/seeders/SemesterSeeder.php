<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use App\Modules\Akademik\Semester\Models\Semester;
use Illuminate\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantId = $tenant->id;

        $tahunAjaran = TahunAjaran::where(
            'kode',
            '2026/2027'
        )->firstOrFail();

        Semester::updateOrCreate(
            [
                'tenant_id' => $tenantId,
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
                'tenant_id' => $tenantId,
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