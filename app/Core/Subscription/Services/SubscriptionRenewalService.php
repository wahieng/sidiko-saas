<?php

namespace App\Core\Subscription\Services;

use App\Core\Billing\Models\TagihanBilling;
use App\Core\Billing\Services\GenerateBillingService;
use App\Core\Subscription\Models\Langganan;
use Illuminate\Support\Facades\DB;

class SubscriptionRenewalService
{
    public function __construct(
        protected GenerateBillingService $generateBillingService
    ) {
    }

    /**
     * Buat billing renewal untuk subscription.
     *
     * Subscription TIDAK diperpanjang di sini.
     * Perpanjangan hanya terjadi setelah billing PAID.
     */
    public function buatBillingRenewal(
        Langganan $langganan
    ): ?TagihanBilling {
        return DB::transaction(function () use ($langganan) {

            $langganan = Langganan::query()
                ->with('paket')
                ->lockForUpdate()
                ->findOrFail($langganan->id);

            /*
             * Hanya subscription yang masih aktif
             * yang boleh dibuatkan renewal.
             */
            if (! in_array($langganan->status, [
                'trial',
                'active',
            ], true)) {
                return null;
            }

            /*
             * Jangan membuat renewal jika
             * masih terlalu jauh dari akhir periode.
             *
             * Batas default: 7 hari sebelum berakhir.
             */
            if (
                $langganan->periode_berakhir &&
                now()->lt(
                    $langganan->periode_berakhir->copy()->subDays(7)
                )
            ) {
                return null;
            }

            /*
             * Cek apakah billing untuk periode berikutnya
             * sudah pernah dibuat.
             */
            $periodeMulai = $langganan->periode_berakhir
                ->copy()
                ->addDay();

            $sudahAda = TagihanBilling::query()
                ->where('langganan_id', $langganan->id)
                ->whereDate('periode_mulai', $periodeMulai)
                ->exists();

            if ($sudahAda) {
                return null;
            }

            /*
             * GenerateBillingService yang menentukan
             * periode dan nominal billing.
             */
            return $this->generateBillingService->generate(
                $langganan
            );
        });
    }
}