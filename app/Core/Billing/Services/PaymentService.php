<?php

namespace App\Core\Billing\Services;

use App\Core\Billing\Models\PembayaranBilling;
use App\Core\Billing\Models\TagihanBilling;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected BillingStatusService $statusService
    ) {
    }

    public function pay(
        TagihanBilling $tagihan,
        array $data
    ): PembayaranBilling {
        return DB::transaction(function () use ($tagihan, $data) {

            /*
             * Lock tagihan selama proses pembayaran.
             * Mencegah dua pembayaran bersamaan
             * terhadap tagihan yang sama.
             */
            $tagihan = TagihanBilling::query()
                ->lockForUpdate()
                ->findOrFail($tagihan->id);

            /*
             * Tagihan PAID dan CANCELLED
             * tidak boleh dibayar lagi.
             */
            if (in_array($tagihan->status, [
                BillingStatusService::PAID,
                BillingStatusService::CANCELLED,
            ], true)) {
                throw new RuntimeException(
                    'Tagihan sudah lunas atau sudah dibatalkan.'
                );
            }

            $jumlah = (float) ($data['jumlah'] ?? 0);

            if ($jumlah <= 0) {
                throw new RuntimeException(
                    'Jumlah pembayaran harus lebih dari 0.'
                );
            }

            /*
             * Hitung total pembayaran yang sudah PAID.
             */
            $totalDibayar = (float) $tagihan
                ->pembayaran()
                ->where('status', 'PAID')
                ->sum('jumlah');

            $sisa = (float) $tagihan->total - $totalDibayar;

            /*
             * Cegah pembayaran melebihi sisa tagihan.
             */
            if ($jumlah > $sisa) {
                throw new RuntimeException(
                    'Jumlah pembayaran melebihi sisa tagihan.'
                );
            }

            /*
             * Buat pembayaran.
             */
            $pembayaran = PembayaranBilling::create([
                'billing_tagihan_id' => $tagihan->id,

                'nomor_pembayaran' => $data['nomor_pembayaran']
                    ?? $this->generateNomorPembayaran(),

                'tanggal_pembayaran' => $data['tanggal_pembayaran']
                    ?? now()->toDateString(),

                'jumlah' => $jumlah,

                'metode' => $data['metode']
                    ?? 'TRANSFER',

                'referensi' => $data['referensi']
                    ?? null,

                'status' => 'PAID',

                'catatan' => $data['catatan']
                    ?? null,
            ]);

            /*
             * BillingStatusService bertanggung jawab
             * menentukan status billing dan menjalankan
             * proses subscription ketika PAID.
             *
             * Contoh:
             *
             * UNPAID  -> PARTIAL
             * UNPAID  -> PAID
             * PARTIAL -> PAID
             */
            $this->statusService->sync(
                $tagihan,
                $pembayaran
            );

            /*
             * Ambil ulang billing setelah status
             * dan subscription diproses.
             */
            $tagihan->refresh();

            return $pembayaran->load('tagihan');
        });
    }

    protected function generateNomorPembayaran(): string
    {
        return 'PAY-' .
            now()->format('Ym') . '-' .
            strtoupper(Str::random(8));
    }
}