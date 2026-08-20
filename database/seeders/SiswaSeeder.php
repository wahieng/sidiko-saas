<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\Siswa\Models\Siswa;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantId = $tenant->id;

        $siswa = [
            [
                'nis' => '10001',
                'nisn' => '0012345678',
                'nik' => '3300000000000001',
                'no_kk' => '3300000000000002',
                'nama' => 'Ahmad Fauzan',
                'nama_panggilan' => 'Ahmad',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '2013-05-10',
                'agama' => 'Islam',
                'no_hp' => '081234567890',
                'email' => 'ahmad@example.com',
                'alamat' => 'Jl. Pendidikan No. 1',
                'rt' => '001',
                'rw' => '002',
                'desa' => 'Sukamaju',
                'kecamatan' => 'Sukamakmur',
                'kabupaten' => 'Semarang',
                'provinsi' => 'Jawa Tengah',
                'kode_pos' => '50000',
                'anak_ke' => 1,
                'jumlah_saudara' => 2,
                'jenis_tinggal' => 'Bersama Orang Tua',
                'transportasi' => 'Sepeda Motor',
                'kebutuhan_khusus' => null,
                'foto' => null,
            ],
            [
                'nis' => '10002',
                'nisn' => '0012345679',
                'nik' => '3300000000000003',
                'no_kk' => '3300000000000004',
                'nama' => 'Siti Aisyah',
                'nama_panggilan' => 'Siti',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '2013-08-15',
                'agama' => 'Islam',
                'no_hp' => '081234567891',
                'email' => 'siti@example.com',
                'alamat' => 'Jl. Pendidikan No. 2',
                'rt' => '002',
                'rw' => '003',
                'desa' => 'Sukamaju',
                'kecamatan' => 'Sukamakmur',
                'kabupaten' => 'Semarang',
                'provinsi' => 'Jawa Tengah',
                'kode_pos' => '50000',
                'anak_ke' => 2,
                'jumlah_saudara' => 2,
                'jenis_tinggal' => 'Bersama Orang Tua',
                'transportasi' => 'Antar Jemput',
                'kebutuhan_khusus' => null,
                'foto' => null,
            ],
        ];

        foreach ($siswa as $data) {
            Siswa::withoutGlobalScopes()
                ->updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'nis' => $data['nis'],
                    ],
                    $data + [
                        'tenant_id' => $tenantId,
                    ]
                );
        }
    }
}