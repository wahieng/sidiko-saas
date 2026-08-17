<?php

namespace App\Core\Billing\Services;

use App\Core\Billing\Models\TagihanBilling;
use App\Core\Subscription\Models\Langganan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GenerateBillingService
{
    public function __construct(
        protected BillingService $billingService
    ) {
    }

    /**
     * Generate tagihan subscription.
     *
     * Trial:
     *   Tagihan pertama dimulai setelah periode trial berakhir.
     *
     * Active:
     *   Tagihan berikutnya dimulai setelah periode subscription terakhir.
     */
    public function generate(
        Langganan $langganan
    ): TagihanBilling {
        return DB::transaction(function () use ($langganan) {

            $langganan->loadMissing('paket');

            $paket = $langganan->paket;

            if (! $paket) {
                throw new RuntimeException(
                    'Paket subscription tidak ditemukan.'
                );
            }

            /*
             * Tentukan periode billing.
             */
            if ($langganan->status === 'trial') {

                /*
                 * Trial selesai pada periode_berakhir.
                 *
                 * Billing pertama dimulai sehari setelah
                 * periode trial berakhir.
                 */
                $periodeMulai = Carbon::parse(
                    $langganan->periode_berakhir
                )->addDay();

            } else {

                /*
                 * Subscription aktif:
                 * billing berikutnya dimulai sehari setelah
                 * periode terakhir.
                 */
                $periodeMulai = Carbon::parse(
                    $langganan->periode_berakhir
                )->addDay();
            }

            $periodeBerakhir = $this->hitungPeriodeBerakhir(
                $periodeMulai,
                $paket->siklus_tagihan
            );

            /*
             * Cegah duplicate billing.
             */
            $sudahAda = TagihanBilling::query()
                ->where('langganan_id', $langganan->id)
                ->whereDate('periode_mulai', $periodeMulai)
                ->whereDate('periode_berakhir', $periodeBerakhir)
                ->exists();

            if ($sudahAda) {
                throw new RuntimeException(
                    'Tagihan untuk periode tersebut sudah dibuat.'
                );
            }

            $subtotal = (float) $paket->harga;
            $diskon = 0;
            $total = $subtotal - $diskon;

            return $this->billingService->create([
                'langganan_id' => $langganan->id,
                'paket_langganan_id' => $paket->id,

                'tanggal_tagihan' => now()->toDateString(),

                'jatuh_tempo' => now()
                    ->addDays(7)
                    ->toDateString(),

                'periode_mulai' => $periodeMulai->toDateString(),
                'periode_berakhir' => $periodeBerakhir->toDateString(),

                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'total' => $total,

                'status' => 'UNPAID',

                'detail' => [
                    [
                        'deskripsi' => 'Langganan ' . $paket->nama,
                        'qty' => 1,
                        'harga' => $subtotal,
                        'subtotal' => $subtotal,
                    ],
                ],
            ]);
        });
    }

    /**
     * Hitung periode berakhir berdasarkan siklus paket.
     */
    protected function hitungPeriodeBerakhir(
        Carbon $periodeMulai,
        ?string $siklus
    ): Carbon {
        return match ($siklus) {

            'bulanan' => $periodeMulai
                ->copy()
                ->addMonth()
                ->subDay(),

            'tahunan' => $periodeMulai
                ->copy()
                ->addYear()
                ->subDay(),

            'semester' => $periodeMulai
                ->copy()
                ->addMonths(6)
                ->subDay(),

            default => throw new RuntimeException(
                "Siklus billing '{$siklus}' tidak didukung."
            ),
        };
    }
}