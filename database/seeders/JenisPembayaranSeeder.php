<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantId = $tenant->id;

        $data = [
            [
                'tenant_id' => $tenantId,
                'kode' => 'SPP',
                'nama' => 'Sumbangan Pembinaan Pendidikan',
                'kategori' => 'BULANAN',
                'keterangan' => 'Pembayaran pendidikan bulanan siswa.',
                'aktif' => true,
            ],
            [
                'tenant_id' => $tenantId,
                'kode' => 'UTS',
                'nama' => 'Ujian Tengah Semester',
                'kategori' => 'SEMESTER',
                'keterangan' => 'Pembayaran Ujian Tengah Semester.',
                'aktif' => true,
            ],
            [
                'tenant_id' => $tenantId,
                'kode' => 'UAS',
                'nama' => 'Ujian Akhir Semester',
                'kategori' => 'SEMESTER',
                'keterangan' => 'Pembayaran Ujian Akhir Semester.',
                'aktif' => true,
            ],
            [
                'tenant_id' => $tenantId,
                'kode' => 'DAFTAR_ULANG',
                'nama' => 'Daftar Ulang',
                'kategori' => 'TAHUNAN',
                'keterangan' => 'Pembayaran daftar ulang siswa.',
                'aktif' => true,
            ],
            [
                'tenant_id' => $tenantId,
                'kode' => 'GEDUNG',
                'nama' => 'Uang Gedung',
                'kategori' => 'SEKALI',
                'keterangan' => 'Pembayaran uang gedung satu kali.',
                'aktif' => true,
            ],
        ];

        foreach ($data as $item) {
            DB::table('jenis_pembayaran')->updateOrInsert(
                [
                    'tenant_id' => $tenantId,
                    'kode' => $item['kode'],
                ],
                [
                    'nama' => $item['nama'],
                    'kategori' => $item['kategori'],
                    'keterangan' => $item['keterangan'],
                    'aktif' => $item['aktif'],
                ]
            );
        }
    }
}