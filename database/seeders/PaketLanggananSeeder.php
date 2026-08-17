<?php

namespace Database\Seeders;

use App\Core\Subscription\Models\FiturPaket;
use App\Core\Subscription\Models\PaketLangganan;
use Illuminate\Database\Seeder;

class PaketLanggananSeeder extends Seeder
{
    public function run(): void
    {
        $paket = [
            [
                'kode' => 'BASIC',
                'nama' => 'Basic',
                'deskripsi' => 'Paket dasar untuk sekolah dengan kebutuhan operasional sederhana.',
                'harga' => 150000,
                'siklus_tagihan' => 'bulanan',
                'batas_siswa' => 300,
                'batas_pengguna' => 20,
                'batas_penyimpanan' => 5,
                'status' => true,
            ],
            [
                'kode' => 'PRO',
                'nama' => 'Professional',
                'deskripsi' => 'Paket lengkap untuk sekolah yang membutuhkan fitur manajemen lebih luas.',
                'harga' => 300000,
                'siklus_tagihan' => 'bulanan',
                'batas_siswa' => 1000,
                'batas_pengguna' => 50,
                'batas_penyimpanan' => 20,
                'status' => true,
            ],
            [
                'kode' => 'ENTERPRISE',
                'nama' => 'Enterprise',
                'deskripsi' => 'Paket untuk sekolah dengan kebutuhan dan kapasitas yang lebih besar.',
                'harga' => 750000,
                'siklus_tagihan' => 'bulanan',
                'batas_siswa' => null,
                'batas_pengguna' => null,
                'batas_penyimpanan' => 100,
                'status' => true,
            ],
        ];

        foreach ($paket as $data) {

            PaketLangganan::updateOrCreate(
                [
                    'kode' => $data['kode'],
                ],
                $data
            );
        }

        $fitur = [

            'BASIC' => [
                'dashboard',
                'master',
                'siswa',
                'akademik',
                'keuangan',
                'laporan',
            ],

            'PRO' => [
                'dashboard',
                'master',
                'siswa',
                'akademik',
                'keuangan',
                'laporan',
                'pengaturan',
                'notifikasi',
                'backup',
            ],

            'ENTERPRISE' => [
                'dashboard',
                'master',
                'siswa',
                'akademik',
                'keuangan',
                'laporan',
                'pengaturan',
                'notifikasi',
                'backup',
                'api',
                'integrasi',
            ],
        ];

        foreach ($fitur as $kodePaket => $daftarFitur) {

            $paketModel = PaketLangganan::where(
                'kode',
                $kodePaket
            )->first();

            foreach ($daftarFitur as $kodeFitur) {

                FiturPaket::updateOrCreate(
                    [
                        'paket_langganan_id' => $paketModel->id,
                        'kode_fitur' => $kodeFitur,
                    ],
                    [
                        'aktif' => true,
                    ]
                );
            }
        }
    }
}