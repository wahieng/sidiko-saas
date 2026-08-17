<?php

namespace Tests\Feature;

use App\Core\Billing\Services\PaymentService;
use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Subscription\Services\SubscriptionRenewalService;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_billing_supports_partial_and_full_payment(): void
    {
        /*
         * Jalankan seeder utama SIDIKO.
         */
        $this->seed();

        /*
         * Ambil tenant DEMO dari seeder.
         * Jangan bergantung pada ID tertentu.
         */
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        /*
         * Ambil paket dari data seeder.
         */
        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        /*
         * Aktifkan tenant context.
         */
        app(TenantContext::class)->set($tenant);

        /*
         * Buat subscription test.
         */
        $mulai = now()->subDays(10);
        $berakhir = now()->subDays(3);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'active',
            'mulai_pada' => $mulai,
            'trial_berakhir_pada' => null,
            'periode_mulai' => $mulai->toDateString(),
            'periode_berakhir' => $berakhir->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        /*
         * ========================================================
         * GENERATE BILLING
         * ========================================================
         */

        $billing = app(
            SubscriptionRenewalService::class
        )->buatBillingRenewal($langganan);

        $this->assertNotNull($billing);

        $this->assertSame(
            'UNPAID',
            $billing->status
        );

        $this->assertSame(
            (float) $paket->harga,
            (float) $billing->total
        );

        $this->assertSame(
            $berakhir->copy()->addDay()->toDateString(),
            $billing->periode_mulai->toDateString()
        );

        /*
         * ========================================================
         * PEMBAYARAN 50%
         * ========================================================
         */

        $setengah = round(
            (float) $billing->total / 2,
            2
        );

        $pembayaranPertama = app(
            PaymentService::class
        )->pay($billing, [
            'jumlah' => $setengah,
            'metode' => 'TRANSFER',
            'referensi' => 'TEST-PARTIAL-001',
            'catatan' => 'Test pembayaran sebagian',
        ]);

        $billing->refresh();
        $langganan->refresh();

        $this->assertSame(
            'PAID',
            $pembayaranPertama->status
        );

        $this->assertSame(
            'PARTIAL',
            $billing->status
        );

        /*
         * Subscription belum boleh diperpanjang.
         */
        $this->assertSame(
            $berakhir->toDateString(),
            $langganan->periode_berakhir->toDateString()
        );

        /*
         * ========================================================
         * PEMBAYARAN SISA
         * ========================================================
         */

        $totalDibayar = (float) $billing
            ->pembayaran()
            ->where('status', 'PAID')
            ->sum('jumlah');

        $sisa = (float) $billing->total - $totalDibayar;

        $pembayaranKedua = app(
            PaymentService::class
        )->pay($billing, [
            'jumlah' => $sisa,
            'metode' => 'TRANSFER',
            'referensi' => 'TEST-PARTIAL-002',
            'catatan' => 'Test pelunasan',
        ]);

        $billing->refresh();
        $langganan->refresh();

        /*
         * ========================================================
         * HASIL AKHIR
         * ========================================================
         */

        $this->assertSame(
            'PAID',
            $pembayaranKedua->status
        );

        $this->assertSame(
            'PAID',
            $billing->status
        );

        /*
         * Setelah lunas, subscription harus
         * mengikuti periode billing.
         */
        $this->assertSame(
            $billing->periode_mulai->toDateString(),
            $langganan->periode_mulai->toDateString()
        );

        $this->assertSame(
            $billing->periode_berakhir->toDateString(),
            $langganan->periode_berakhir->toDateString()
        );
    }
}