<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        TahunAjaran::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'kode' => '2026/2027',
            ],
            [
                'nama' => 'Tahun Ajaran 2026/2027',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2027-06-30',
                'aktif' => true,
            ]
        );
    }
}