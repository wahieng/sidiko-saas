<?php

namespace Database\Seeders;

use App\Modules\Siswa\OrangTua\Models\OrangTua;
use App\Modules\Siswa\Siswa\Models\Siswa;
use Illuminate\Database\Seeder;

class OrangTuaSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = Siswa::query()
            ->whereIn('nis', [
                '10001',
                '10002',
            ])
            ->get()
            ->keyBy('nis');

        $data = [
            [
                'nis' => '10001',
                'hubungan' => 'AYAH',
                'nama' => 'Budi Santoso',
                'nik' => '3300000000000011',
                'no_kk' => '3300000000000002',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1985-03-12',
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Wiraswasta',
                'penghasilan' => 5000000,
                'no_hp' => '081234567801',
                'email' => 'budi@example.com',
                'alamat' => 'Jl. Pendidikan No. 1',
                'keterangan' => null,
            ],
            [
                'nis' => '10001',
                'hubungan' => 'IBU',
                'nama' => 'Dewi Lestari',
                'nik' => '3300000000000012',
                'no_kk' => '3300000000000002',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1987-07-20',
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'penghasilan' => null,
                'no_hp' => '081234567802',
                'email' => 'dewi@example.com',
                'alamat' => 'Jl. Pendidikan No. 1',
                'keterangan' => null,
            ],
            [
                'nis' => '10002',
                'hubungan' => 'AYAH',
                'nama' => 'Hendra Wijaya',
                'nik' => '3300000000000013',
                'no_kk' => '3300000000000004',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1984-01-15',
                'pendidikan' => 'S1',
                'pekerjaan' => 'Karyawan Swasta',
                'penghasilan' => 6000000,
                'no_hp' => '081234567803',
                'email' => 'hendra@example.com',
                'alamat' => 'Jl. Pendidikan No. 2',
                'keterangan' => null,
            ],
            [
                'nis' => '10002',
                'hubungan' => 'IBU',
                'nama' => 'Rina Wati',
                'nik' => '3300000000000014',
                'no_kk' => '3300000000000004',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1986-11-05',
                'pendidikan' => 'SMA',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'penghasilan' => null,
                'no_hp' => '081234567804',
                'email' => 'rina@example.com',
                'alamat' => 'Jl. Pendidikan No. 2',
                'keterangan' => null,
            ],
        ];

        foreach ($data as $item) {
            $siswaItem = $siswa->get($item['nis']);

            if (! $siswaItem) {
                continue;
            }

            /*
             * Orang Tua harus berada pada tenant
             * yang sama dengan siswa.
             */
            $tenantId = $siswaItem->tenant_id;

            OrangTua::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'siswa_id' => $siswaItem->id,
                    'hubungan' => $item['hubungan'],
                ],
                [
                    'nama' => $item['nama'],
                    'nik' => $item['nik'],
                    'no_kk' => $item['no_kk'],
                    'tempat_lahir' => $item['tempat_lahir'],
                    'tanggal_lahir' => $item['tanggal_lahir'],
                    'pendidikan' => $item['pendidikan'],
                    'pekerjaan' => $item['pekerjaan'],
                    'penghasilan' => $item['penghasilan'],
                    'no_hp' => $item['no_hp'],
                    'email' => $item['email'],
                    'alamat' => $item['alamat'],
                    'keterangan' => $item['keterangan'],
                ]
            );
        }
    }
}