<?php

namespace App\Core\Subscription\Services;

use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Tenant\Models\Tenant;
use App\Core\Billing\Models\TagihanBilling;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubscriptionService
{
    /**
     * Buat subscription trial untuk tenant.
     */
    public function buatTrial(
        Tenant $tenant,
        PaketLangganan $paket,
        int $hariTrial = 14
    ): Langganan {
        return DB::transaction(function () use (
            $tenant,
            $paket,
            $hariTrial
        ) {

            $subscriptionAktif = Langganan::query()
                ->where('tenant_id', $tenant->id)
                ->whereIn('status', [
                    'trial',
                    'active',
                    'past_due',
                    'suspended',
                ])
                ->exists();

            if ($subscriptionAktif) {
                throw new RuntimeException(
                    'Tenant sudah memiliki subscription aktif.'
                );
            }

            $mulai = Carbon::now();

            return Langganan::create([
                'tenant_id' => $tenant->id,
                'paket_langganan_id' => $paket->id,
                'status' => 'trial',
                'mulai_pada' => $mulai,
                'trial_berakhir_pada' => $mulai->copy()->addDays($hariTrial),
                'periode_mulai' => $mulai->toDateString(),
                'periode_berakhir' => $mulai
                    ->copy()
                    ->addDays($hariTrial)
                    ->toDateString(),
            ]);
        });
    }


    /**
     * Aktivasi subscription.
     */
    public function aktifkan(Langganan $langganan): Langganan
    {
        return DB::transaction(function () use ($langganan) {

            $langganan->update([
                'status' => 'active',
            ]);

            return $langganan->fresh([
                'tenant',
                'paket',
            ]);
        });
    }


    /**
     * Batalkan subscription.
     */
    public function batalkan(Langganan $langganan): Langganan
    {
        return DB::transaction(function () use ($langganan) {

            $langganan->update([
                'status' => 'cancelled',
                'dibatalkan_pada' => now(),
            ]);

            return $langganan->fresh([
                'tenant',
                'paket',
            ]);
        });
    }


    /**
     * Suspend subscription.
     */
    public function suspend(Langganan $langganan): Langganan
    {
        $langganan->update([
            'status' => 'suspended',
        ]);

        return $langganan->fresh([
            'tenant',
            'paket',
        ]);
    }


    /**
     * Tandai subscription expired.
     */
    public function expire(Langganan $langganan): Langganan
    {
        $langganan->update([
            'status' => 'expired',
        ]);

        return $langganan->fresh([
            'tenant',
            'paket',
        ]);
    }


    /**
     * Ambil subscription aktif milik tenant.
     */
    public function aktifUntukTenant(Tenant $tenant): ?Langganan
    {
        return Langganan::query()
            ->with([
                'paket',
                'paket.fitur',
            ])
            ->where('tenant_id', $tenant->id)
            ->whereIn('status', [
                'trial',
                'active',
                'past_due',
                'suspended',
            ])
            ->latest('id')
            ->first();
    }

    /**
     * Aktifkan atau perpanjang subscription
     * setelah billing berstatus PAID.
     */
    public function aktifkanDariBilling(
        TagihanBilling $tagihan
    ): Langganan {
        return DB::transaction(function () use ($tagihan) {

            $langganan = Langganan::query()
                ->lockForUpdate()
                ->findOrFail($tagihan->langganan_id);

            $langganan->update([
                'status' => 'active',
                'periode_mulai' => $tagihan->periode_mulai,
                'periode_berakhir' => $tagihan->periode_berakhir,
                'dibatalkan_pada' => null,
            ]);

            return $langganan->fresh([
                'tenant',
                'paket',
            ]);
        });
    }
}