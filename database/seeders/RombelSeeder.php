<?php

namespace Database\Seeders;

use App\Modules\Akademik\Models\Rombel;
use Illuminate\Database\Seeder;

class RombelSeeder extends Seeder
{
    public function run(): void
    {
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
            Rombel::updateOrCreate(
                ['kode' => $data['kode']],
                $data
            );
        }
    }
}