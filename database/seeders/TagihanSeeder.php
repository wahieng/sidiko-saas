<?php

namespace Database\Seeders;

use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Database\Seeder;

class TagihanSeeder extends Seeder
{
    public function run(): void
    {
        $tanggalTagihan = '2026-07-01';
        $tanggalJatuhTempo = '2026-07-31';

        $siswaTahunList = SiswaTahun::query()
            ->where('status', 'AKTIF')
            ->get();

        foreach ($siswaTahunList as $siswaTahun) {
            $tarif = TarifPembayaran::query()
                ->where('kelompok_rombel_id', $siswaTahun->kelompok_rombel_id)
                ->where('aktif', true)
                ->first();

            if (! $tarif) {
                continue;
            }

            $diskon = DiskonPembayaran::query()
                ->where('siswa_tahun_id', $siswaTahun->id)
                ->where('tarif_pembayaran_id', $tarif->id)
                ->where('is_active', true)
                ->where(function ($query) use ($tanggalTagihan) {
                    $query
                        ->whereNull('tanggal_mulai')
                        ->orWhereDate('tanggal_mulai', '<=', $tanggalTagihan);
                })
                ->where(function ($query) use ($tanggalTagihan) {
                    $query
                        ->whereNull('tanggal_selesai')
                        ->orWhereDate('tanggal_selesai', '>=', $tanggalTagihan);
                })
                ->first();

            $nominalAwal = (float) $tarif->nominal;

            $tipeDiskon = null;
            $nilaiDiskon = 0;
            $nominalDiskon = 0;

            if ($diskon) {
                $tipeDiskon = $diskon->tipe_diskon;
                $nilaiDiskon = (float) $diskon->nilai;

                $nominalDiskon = match ($tipeDiskon) {
                    'PERSEN' => $nominalAwal * ($nilaiDiskon / 100),
                    'NOMINAL' => $nilaiDiskon,
                    default => 0,
                };

                $nominalDiskon = min(
                    $nominalDiskon,
                    $nominalAwal
                );
            }

            $nominal = $nominalAwal - $nominalDiskon;

            $nomorTagihan = sprintf(
                'TG/%s/%s/%06d',
                date('Ym', strtotime($tanggalTagihan)),
                $siswaTahun->id,
                $siswaTahun->id
            );

            Tagihan::query()->updateOrCreate(
                [
                    'nomor_tagihan' => $nomorTagihan,
                ],
                [
                    'siswa_tahun_id' => $siswaTahun->id,
                    'tarif_pembayaran_id' => $tarif->id,

                    'nominal_awal' => $nominalAwal,

                    'tipe_diskon' => $tipeDiskon,
                    'nilai_diskon' => $nilaiDiskon,
                    'nominal_diskon' => $nominalDiskon,

                    'nominal' => $nominal,
                    'jumlah_dibayar' => 0,
                    'sisa_tagihan' => $nominal,

                    'tanggal_tagihan' => $tanggalTagihan,
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,

                    'status' => 'BELUM_BAYAR',

                    'keterangan' => $diskon?->keterangan,
                ]
            );
        }
    }
}