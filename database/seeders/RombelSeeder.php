<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Akademik\Rombel\Models\Rombel;
use Illuminate\Database\Seeder;

class RombelSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantId = $tenant->id;

        $rombel = [
            [
                'kode' => 'VII',
                'nama' => 'VII',
                'urutan' => 7,
                'aktif' => true,
            ],
            [
                'kode' => 'VIII',
                'nama' => 'VIII',
                'urutan' => 8,
                'aktif' => true,
            ],
            [
                'kode' => 'IX',
                'nama' => 'IX',
                'urutan' => 9,
                'aktif' => true,
            ],
        ];

        foreach ($rombel as $data) {
            Rombel::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'kode' => $data['kode'],
                    ],
                    [
                        'nama' => $data['nama'],
                        'urutan' => $data['urutan'],
                        'aktif' => $data['aktif'],
                    ]
                );
        }
    }
}