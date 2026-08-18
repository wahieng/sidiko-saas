<?php

namespace Database\Seeders;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\Siswa\Models\Siswa;
use App\Modules\Siswa\Wali\Models\Wali;
use Illuminate\Database\Seeder;

class WaliSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        $siswa = Siswa::query()
            ->where('nis', '10001')
            ->first();

        if (! $siswa) {
            return;
        }

        Wali::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
            ],
            [
                'nama' => 'Budi Santoso',
                'nik' => '3300000000000010',
                'hubungan' => 'Paman',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1980-03-15',
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Wiraswasta',
                'penghasilan' => 5000000,
                'no_hp' => '081234567899',
                'email' => 'budi@example.com',
                'alamat' => 'Jl. Pendidikan No. 10',
                'keterangan' => 'Wali siswa',
            ]
        );
    }
}