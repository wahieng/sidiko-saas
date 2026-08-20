<?php

namespace Tests\Feature\Core\Subscription;

use App\Core\Billing\Models\TagihanBilling;
use App\Core\Billing\Services\BillingStatusService;
use App\Core\Billing\Services\PaymentService;
use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Subscription\Services\SubscriptionRenewalService;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class SubscriptionRenewalCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_generates_renewal_billing_for_subscription_near_expiry(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        $tenantContext = app(TenantContext::class);

        /*
        * Tenant context harus aktif sebelum
        * membuat model yang menggunakan BelongsToTenant.
        */
        $tenantContext->set($tenant);

        $this->assertTrue(
            $tenantContext->has()
        );

        /*
        * Subscription berakhir 3 hari lagi.
        * Masuk window renewal 7 hari.
        */
        $mulai = now()->subDays(27);
        $berakhir = now()->addDays(3);

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
        * Command akan memproses tenant dan
        * membersihkan context setelah selesai.
        */
        $this->artisan('subscription:generate-renewals')
            ->assertExitCode(0);

        /*
        * Billing renewal harus dibuat.
        *
        * assertDatabaseHas() tidak menggunakan
        * Eloquent global scope.
        */
        $this->assertDatabaseHas('billing_tagihan', [
            'langganan_id' => $langganan->id,
            'status' => 'UNPAID',
        ]);

        /*
        * Command sudah membersihkan tenant context.
        * Aktifkan kembali context hanya untuk query
        * TagihanBilling yang menggunakan TenantScope.
        */
        $tenantContext->set($tenant);

        $jumlahBilling = TagihanBilling::query()
            ->where('langganan_id', $langganan->id)
            ->count();

        $this->assertSame(
            1,
            $jumlahBilling
        );

        /*
        * Bersihkan kembali context.
        */
        $tenantContext->clear();

        /*
        * Tenant context harus bersih
        * setelah command selesai.
        */
        $this->assertFalse(
            $tenantContext->has()
        );
    }

    public function test_command_does_not_create_duplicate_renewal_billing(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        $tenantContext = app(TenantContext::class);

        /*
        * Tenant context harus aktif sebelum
        * membuat subscription.
        */
        $tenantContext->set($tenant);

        $this->assertTrue(
            $tenantContext->has()
        );

        /*
        * Subscription berakhir 3 hari lagi.
        */
        $mulai = now()->subDays(27);
        $berakhir = now()->addDays(3);

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
        * Command pertama harus membuat
        * satu billing renewal.
        */
        $this->artisan('subscription:generate-renewals')
            ->assertExitCode(0);

        $this->assertDatabaseHas('billing_tagihan', [
            'langganan_id' => $langganan->id,
            'status' => 'UNPAID',
        ]);

        /*
        * Command kedua tidak boleh membuat
        * billing renewal tambahan.
        */
        $this->artisan('subscription:generate-renewals')
            ->assertExitCode(0);

        /*
        * Kedua command membersihkan tenant context.
        * Aktifkan kembali context hanya untuk query
        * TagihanBilling yang menggunakan TenantScope.
        */
        $tenantContext->set($tenant);

        $jumlahBilling = TagihanBilling::query()
            ->where('langganan_id', $langganan->id)
            ->count();

        $this->assertSame(
            1,
            $jumlahBilling
        );

        /*
        * Bersihkan kembali context.
        */
        $tenantContext->clear();

        /*
        * Tenant context harus tetap bersih
        * setelah command selesai.
        */
        $this->assertFalse(
            $tenantContext->has()
        );
    }

    public function test_payment_cannot_exceed_remaining_billing_amount(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

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

        $billing = app(
            SubscriptionRenewalService::class
        )->buatBillingRenewal($langganan);

        $this->assertNotNull($billing);

        /*
         * Bayar Rp100.000.
         */
        app(PaymentService::class)->pay($billing, [
            'jumlah' => 100000,
            'metode' => 'TRANSFER',
            'referensi' => 'OVERPAY-TEST-001',
        ]);

        $billing->refresh();

        $this->assertSame(
            'PARTIAL',
            $billing->status
        );

        /*
         * Total Rp150.000 - Rp100.000 = Rp50.000.
         */
        $sisa = (float) $billing->total
            - (float) $billing
                ->pembayaran()
                ->where('status', 'PAID')
                ->sum('jumlah');

        $this->assertSame(
            50000.0,
            $sisa
        );

        /*
         * Coba bayar Rp60.000.
         * Harus ditolak.
         */
        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Jumlah pembayaran melebihi sisa tagihan.'
        );

        app(PaymentService::class)->pay($billing, [
            'jumlah' => 60000,
            'metode' => 'TRANSFER',
            'referensi' => 'OVERPAY-TEST-002',
        ]);
    }

    public function test_unpaid_billing_becomes_overdue_after_due_date(): void
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
            'periode_berakhir' => now()->subDays(3)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $billing = app(
            SubscriptionRenewalService::class
        )->buatBillingRenewal($langganan);

        $this->assertNotNull($billing);

        $billing->update([
            'jatuh_tempo' => now()->subDay(),
        ]);

        $billing = app(
            BillingStatusService::class
        )->sync($billing);

        $this->assertSame(
            BillingStatusService::OVERDUE,
            $billing->status
        );
    }

    public function test_paid_billing_does_not_become_overdue(): void
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
            'periode_berakhir' => now()->subDays(3)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $billing = app(
            SubscriptionRenewalService::class
        )->buatBillingRenewal($langganan);

        $this->assertNotNull($billing);

        $billing->update([
            'jatuh_tempo' => now()->subDay(),
        ]);

        app(PaymentService::class)->pay($billing, [
            'jumlah' => $billing->total,
            'metode' => 'TRANSFER',
            'referensi' => 'OVERDUE-PAID-TEST',
        ]);

        $billing->refresh();

        $this->assertSame(
            BillingStatusService::PAID,
            $billing->status
        );

        $billing = app(
            BillingStatusService::class
        )->sync($billing);

        $this->assertSame(
            BillingStatusService::PAID,
            $billing->status
        );
    }

    public function test_cancelled_billing_does_not_become_overdue(): void
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
            'periode_berakhir' => now()->subDays(3)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $billing = app(
            SubscriptionRenewalService::class
        )->buatBillingRenewal($langganan);

        $this->assertNotNull($billing);

        $billing->update([
            'jatuh_tempo' => now()->subDay(),
        ]);

        $billing = app(
            BillingStatusService::class
        )->cancel($billing, 'Test cancellation');

        $this->assertSame(
            BillingStatusService::CANCELLED,
            $billing->status
        );

        $billing = app(
            BillingStatusService::class
        )->sync($billing);

        $this->assertSame(
            BillingStatusService::CANCELLED,
            $billing->status
        );
    }

    public function test_subscription_from_different_tenant_cannot_generate_renewal(): void
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $paket = PaketLangganan::query()
            ->where('status', true)
            ->firstOrFail();

        $tenantLain = Tenant::create([
            'name' => 'Tenant Renewal Test',
            'code' => 'TEST-RENEWAL-OTHER',
            'slug' => 'test-renewal-other',
            'email' => 'test-renewal-other@example.test',
            'phone' => '0800000002',
            'address' => 'Alamat Tenant Renewal Test',
            'is_active' => true,
        ]);

        app(TenantContext::class)->set($tenantLain);

        $langganan = Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'active',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()->subDays(30)->toDateString(),
            'periode_berakhir' => now()->addDays(3)->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        $this->expectException(\RuntimeException::class);

        app(SubscriptionRenewalService::class)
            ->buatBillingRenewal($langganan);
    }

    public function test_subscription_without_end_period_cannot_generate_renewal(): void
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
            'periode_berakhir' => null,
            'dibatalkan_pada' => null,
        ]);

        $hasil = app(SubscriptionRenewalService::class)
            ->buatBillingRenewal($langganan);

        $this->assertNull($hasil);
    }
}