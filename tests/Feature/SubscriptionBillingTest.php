<?php

namespace Tests\Feature;

use App\Core\Billing\Services\PaymentService;
use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Subscription\Services\SubscriptionRenewalService;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Core\Subscription\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Core\Billing\Models\TagihanBilling;

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

    public function test_active_subscription_can_be_cancelled(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'active',
            'mulai_pada' => now()->subDays(10),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(10)->toDateString(),
            'periode_berakhir' => now()->addDays(20)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $hasil = app(SubscriptionService::class)
            ->batalkan($langganan);

        $this->assertSame(
            'cancelled',
            $hasil->status
        );

        $this->assertNotNull(
            $hasil->dibatalkan_pada
        );
    }

    public function test_active_subscription_can_be_suspended(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'active',
            'mulai_pada' => now()->subDays(10),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(10)->toDateString(),
            'periode_berakhir' => now()->addDays(20)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $hasil = app(SubscriptionService::class)
            ->suspend($langganan);

        $this->assertSame(
            'suspended',
            $hasil->status
        );
    }

    public function test_active_subscription_can_be_expired(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'active',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDays(1)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $hasil = app(SubscriptionService::class)
            ->expire($langganan);

        $this->assertSame(
            'expired',
            $hasil->status
        );
    }

    public function test_paid_billing_can_activate_subscription(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'past_due',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDay()->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $tagihan = \App\Core\Billing\Models\TagihanBilling::create([
            'tenant_id' => $tenant->id,
            'langganan_id' => $langganan->id,
            'paket_langganan_id' => $paket->id,
            'nomor_tagihan' => 'TEST-BILLING-001',
            'tanggal_tagihan' => now()->toDateString(),
            'jatuh_tempo' => now()->addDays(7)->toDateString(),
            'periode_mulai' => now()->toDateString(),
            'periode_berakhir' => now()->addDays(30)->toDateString(),
            'subtotal' => $paket->harga,
            'diskon' => 0,
            'total' => $paket->harga,
            'status' => 'PAID',
            'dibayar_pada' => now(),
            'catatan' => 'Test aktivasi subscription',
        ]);

        $hasil = app(SubscriptionService::class)
            ->aktifkanDariBilling($tagihan);

        $this->assertSame(
            'active',
            $hasil->status
        );

        $this->assertSame(
            $tagihan->periode_mulai->toDateString(),
            $hasil->periode_mulai->toDateString()
        );

        $this->assertSame(
            $tagihan->periode_berakhir->toDateString(),
            $hasil->periode_berakhir->toDateString()
        );
    }

    public function test_unpaid_billing_cannot_activate_subscription(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'past_due',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDay()->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $tagihan = TagihanBilling::create([
            'tenant_id' => $tenant->id,
            'langganan_id' => $langganan->id,
            'paket_langganan_id' => $paket->id,
            'nomor_tagihan' => 'TEST-BILLING-UNPAID-001',
            'tanggal_tagihan' => now()->toDateString(),
            'jatuh_tempo' => now()->addDays(7)->toDateString(),
            'periode_mulai' => now()->toDateString(),
            'periode_berakhir' => now()->addDays(30)->toDateString(),
            'subtotal' => $paket->harga,
            'diskon' => 0,
            'total' => $paket->harga,
            'status' => 'UNPAID',
            'dibayar_pada' => null,
            'catatan' => 'Test billing belum dibayar',
        ]);

        $this->expectException(\RuntimeException::class);

        app(SubscriptionService::class)
            ->aktifkanDariBilling($tagihan);
    }

    public function test_billing_from_different_tenant_cannot_activate_subscription(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        $tenantLain = Tenant::create([
            'name' => 'Tenant Test Lain',
            'code' => 'TEST-OTHER',
            'slug' => 'test-other',
            'email' => 'test-other@example.test',
            'phone' => '0800000001',
            'address' => 'Alamat Tenant Test Lain',
            'is_active' => true,
        ]);

        app(TenantContext::class)->set($tenant);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'past_due',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDay()->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $tagihan = TagihanBilling::create([
            'tenant_id' => $tenantLain->id,
            'langganan_id' => $langganan->id,
            'paket_langganan_id' => $paket->id,
            'nomor_tagihan' => 'TEST-CROSS-TENANT-001',
            'tanggal_tagihan' => now()->toDateString(),
            'jatuh_tempo' => now()->addDays(7)->toDateString(),
            'periode_mulai' => now()->toDateString(),
            'periode_berakhir' => now()->addDays(30)->toDateString(),
            'subtotal' => $paket->harga,
            'diskon' => 0,
            'total' => $paket->harga,
            'status' => 'PAID',
            'dibayar_pada' => now(),
            'catatan' => 'Test cross tenant',
        ]);

        $this->expectException(\RuntimeException::class);

        app(SubscriptionService::class)
            ->aktifkanDariBilling($tagihan);
    }

    public function test_tenant_without_subscription_can_start_trial(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        $langganan = app(SubscriptionService::class)
            ->buatTrial(
                $tenant,
                $paket,
                14
            );

        $this->assertSame(
            $tenant->id,
            $langganan->tenant_id
        );

        $this->assertSame(
            $paket->id,
            $langganan->paket_langganan_id
        );

        $this->assertSame(
            'trial',
            $langganan->status
        );

        $this->assertNotNull(
            $langganan->trial_berakhir_pada
        );

        $this->assertEquals(
            14,
            $langganan->mulai_pada
                ->diffInDays($langganan->trial_berakhir_pada)
        );
    }

    public function test_tenant_with_active_subscription_cannot_start_trial(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'active',
            'mulai_pada' => now()->subDays(10),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(10)->toDateString(),
            'periode_berakhir' => now()->addDays(20)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $this->expectException(\RuntimeException::class);

        app(SubscriptionService::class)
            ->buatTrial(
                $tenant,
                $paket,
                14
            );
    }

    public function test_tenant_with_trial_subscription_cannot_start_another_trial(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'trial',
            'mulai_pada' => now(),
            'trial_berakhir_pada' => now()->addDays(14),
            'periode_mulai' => now()->toDateString(),
            'periode_berakhir' => now()->addDays(14)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $this->expectException(\RuntimeException::class);

        app(SubscriptionService::class)
            ->buatTrial(
                $tenant,
                $paket,
                14
            );
    }

    public function test_tenant_with_past_due_subscription_cannot_start_trial(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'past_due',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDays(1)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $this->expectException(\RuntimeException::class);

        app(SubscriptionService::class)
            ->buatTrial(
                $tenant,
                $paket,
                14
            );
    }

    public function test_tenant_with_suspended_subscription_cannot_start_trial(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'suspended',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDays(1)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $this->expectException(\RuntimeException::class);

        app(SubscriptionService::class)
            ->buatTrial(
                $tenant,
                $paket,
                14
            );
    }

    public function test_tenant_with_cancelled_subscription_can_start_new_trial(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'cancelled',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => now()->subDays(16),
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDays(16)->toDateString(),
            'dibatalkan_pada' => now()->subDays(16),
        ]);

        $langganan = app(SubscriptionService::class)
            ->buatTrial(
                $tenant,
                $paket,
                14
            );

        $this->assertSame(
            'trial',
            $langganan->status
        );

        $this->assertSame(
            $tenant->id,
            $langganan->tenant_id
        );

        $this->assertSame(
            $paket->id,
            $langganan->paket_langganan_id
        );
    }

    public function test_tenant_with_expired_subscription_can_start_new_trial(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'expired',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->subDays(16)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $langganan = app(SubscriptionService::class)
            ->buatTrial(
                $tenant,
                $paket,
                14
            );

        $this->assertSame(
            'trial',
            $langganan->status
        );

        $this->assertSame(
            $tenant->id,
            $langganan->tenant_id
        );

        $this->assertSame(
            $paket->id,
            $langganan->paket_langganan_id
        );
    }
}