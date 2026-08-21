<?php

namespace App\Modules\Keuangan\Tagihan\Services;

use App\Core\TransactionNumber\Services\TransactionNumberService;
use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenerateTagihanService
{
    public function __construct(
        protected TagihanService $tagihanService,
        protected TransactionNumberService $transactionNumberService,
    ) {
    }

    /**
     * Generate satu tagihan untuk siswa.
     */
    public function generate(
        SiswaTahun $siswaTahun,
        TarifPembayaran $tarif,
        string $tanggalTagihan,
        string $tanggalJatuhTempo,
    ): Tagihan {
        return DB::transaction(function () use (
            $siswaTahun,
            $tarif,
            $tanggalTagihan,
            $tanggalJatuhTempo
        ) {
            $period = date('Ym', strtotime($tanggalTagihan));

            $nomorTagihan = $this->transactionNumberService->generate(
                'BILL',
                $period
            );

            $diskon = DiskonPembayaran::query()
                ->where('siswa_tahun_id', $siswaTahun->id)
                ->where('tarif_pembayaran_id', $tarif->id)
                ->where('is_active', true)
                ->where(function ($query) use ($tanggalTagihan) {
                    $query
                        ->whereNull('tanggal_mulai')
                        ->orWhereDate(
                            'tanggal_mulai',
                            '<=',
                            $tanggalTagihan
                        );
                })
                ->where(function ($query) use ($tanggalTagihan) {
                    $query
                        ->whereNull('tanggal_selesai')
                        ->orWhereDate(
                            'tanggal_selesai',
                            '>=',
                            $tanggalTagihan
                        );
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

            return $this->tagihanService->create([
                'siswa_tahun_id' => $siswaTahun->id,
                'tarif_pembayaran_id' => $tarif->id,

                'nomor_tagihan' => $nomorTagihan,

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
            ]);
        });
    }

    /**
     * Generate tagihan untuk seluruh siswa aktif
     * pada kelompok rombel tarif.
     */
    public function generateForAll(
        TarifPembayaran $tarif,
        string $tanggalTagihan,
        string $tanggalJatuhTempo,
    ): Collection {
        return DB::transaction(function () use (
            $tarif,
            $tanggalTagihan,
            $tanggalJatuhTempo
        ) {
            $siswaTahunList = SiswaTahun::query()
                ->where('status', 'AKTIF')
                ->where(
                    'kelompok_rombel_id',
                    $tarif->kelompok_rombel_id
                )
                ->get();

            return $siswaTahunList->map(
                fn (SiswaTahun $siswaTahun) => $this->generate(
                    $siswaTahun,
                    $tarif,
                    $tanggalTagihan,
                    $tanggalJatuhTempo
                )
            );
        });
    }
}