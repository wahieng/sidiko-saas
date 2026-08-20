<?php

namespace Tests\Feature\Core\Billings;

use App\Core\Billing\Models\TagihanBilling;
use App\Core\Billing\Services\GenerateBillingService;
use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Throwable;

class GenerateBillingServiceTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Test Helpers
    |--------------------------------------------------------------------------
    */

    private function setupTenant(): Tenant
    {
        $this->seed();

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);

        return $tenant;
    }

    private function createTestPackage(
        string $siklus = 'bulanan',
        float $harga = 150000
    ): PaketLangganan {
        return PaketLangganan::create([
            'kode' => 'TEST-' . strtoupper($siklus) . '-' . uniqid(),
            'nama' => 'Paket ' . ucfirst($siklus) . ' Test',
            'deskripsi' => 'Paket khusus pengujian billing.',
            'harga' => $harga,
            'siklus_tagihan' => $siklus,
            'batas_siswa' => 300,
            'batas_pengguna' => 20,
            'batas_penyimpanan' => 5,
            'status' => true,
        ]);
    }

    private function createSubscription(
        Tenant $tenant,
        PaketLangganan $paket,
        string $status = 'active',
        ?Carbon $periodeMulai = null,
        ?Carbon $periodeBerakhir = null
    ): Langganan {
        $periodeMulai ??= now()->subDays(30);
        $periodeBerakhir ??= now()->addDays(3);

        return Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => $status,

            'mulai_pada' => $periodeMulai,

            'trial_berakhir_pada' => $status === 'trial'
                ? $periodeBerakhir
                : null,

            'periode_mulai' => $periodeMulai->toDateString(),

            'periode_berakhir' => $periodeBerakhir->toDateString(),

            'dibatalkan_pada' => $status === 'cancelled'
                ? now()
                : null,
        ]);
    }

    private function generateBilling(
        Langganan $langganan
    ): TagihanBilling {
        return app(GenerateBillingService::class)
            ->generate($langganan);
    }

    private function createPackageSubscription(
        Tenant $tenant,
        string $siklus = 'bulanan',
        float $harga = 150000,
        string $status = 'active',
        ?Carbon $periodeMulai = null,
        ?Carbon $periodeBerakhir = null
    ): array {
        $paket = $this->createTestPackage(
            $siklus,
            $harga
        );

        $langganan = $this->createSubscription(
            $tenant,
            $paket,
            $status,
            $periodeMulai,
            $periodeBerakhir
        );

        return [
            'paket' => $paket,
            'langganan' => $langganan,
        ];
    }

    private function assertBasicBilling(
        TagihanBilling $billing,
        Tenant $tenant,
        Langganan $langganan,
        PaketLangganan $paket
    ): void {
        $this->assertInstanceOf(
            TagihanBilling::class,
            $billing
        );

        $this->assertSame(
            $tenant->id,
            $billing->tenant_id
        );

        $this->assertSame(
            $langganan->id,
            $billing->langganan_id
        );

        $this->assertSame(
            $paket->id,
            $billing->paket_langganan_id
        );

        $this->assertSame(
            'UNPAID',
            $billing->status
        );

        $this->assertSame(
            (float) $paket->harga,
            (float) $billing->subtotal
        );

        $this->assertSame(
            0.0,
            (float) $billing->diskon
        );

        $this->assertSame(
            (float) $paket->harga,
            (float) $billing->total
        );

        $this->assertDatabaseHas(
            'billing_tagihan',
            [
                'id' => $billing->id,
                'tenant_id' => $tenant->id,
                'langganan_id' => $langganan->id,
                'paket_langganan_id' => $paket->id,
                'status' => 'UNPAID',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Billing
    |--------------------------------------------------------------------------
    */

    public function test_active_subscription_can_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        [
            'paket' => $paket,
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan',
            150000,
            'active'
        );

        $billing = $this->generateBilling($langganan);

        $this->assertBasicBilling(
            $billing,
            $tenant,
            $langganan,
            $paket
        );

        $periodeMulaiBilling = Carbon::parse(
            $langganan->periode_berakhir
        )->addDay();

        $this->assertSame(
            $periodeMulaiBilling->toDateString(),
            $billing->periode_mulai->toDateString()
        );

        $this->assertSame(
            $periodeMulaiBilling
                ->copy()
                ->addMonth()
                ->subDay()
                ->toDateString(),
            $billing->periode_berakhir->toDateString()
        );
    }

    public function test_trial_subscription_can_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        [
            'paket' => $paket,
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan',
            150000,
            'trial'
        );

        $billing = $this->generateBilling($langganan);

        $this->assertBasicBilling(
            $billing,
            $tenant,
            $langganan,
            $paket
        );

        $periodeMulaiBilling = Carbon::parse(
            $langganan->periode_berakhir
        )->addDay();

        $this->assertSame(
            $periodeMulaiBilling->toDateString(),
            $billing->periode_mulai->toDateString()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Invalid Subscription Status
    |--------------------------------------------------------------------------
    */

    public function test_cancelled_subscription_cannot_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        [
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan',
            150000,
            'cancelled'
        );

        $this->expectException(\RuntimeException::class);

        $this->generateBilling($langganan);
    }

    public function test_suspended_subscription_cannot_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        [
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan',
            150000,
            'suspended'
        );

        $this->expectException(\RuntimeException::class);

        $this->generateBilling($langganan);
    }

    public function test_past_due_subscription_cannot_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        [
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan',
            150000,
            'past_due'
        );

        $this->expectException(\RuntimeException::class);

        $this->generateBilling($langganan);
    }

    public function test_expired_subscription_cannot_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        $periodeMulai = now()->subDays(60);
        $periodeBerakhir = now()->subDays(30);

        [
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan',
            150000,
            'expired',
            $periodeMulai,
            $periodeBerakhir
        );

        $this->expectException(\RuntimeException::class);

        $this->generateBilling($langganan);
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate Protection
    |--------------------------------------------------------------------------
    */

    public function test_duplicate_billing_cannot_be_generated(): void
    {
        $tenant = $this->setupTenant();

        [
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan'
        );

        $billingPertama = $this->generateBilling($langganan);

        $this->assertNotNull($billingPertama);

        $this->expectException(\RuntimeException::class);

        $this->generateBilling($langganan);
    }

    public function test_database_prevents_duplicate_billing_period(): void
    {
        $tenant = $this->setupTenant();

        [
            'paket' => $paket,
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'bulanan'
        );

        $billing = $this->generateBilling($langganan);

        $this->assertDatabaseHas(
            'billing_tagihan',
            [
                'id' => $billing->id,
                'langganan_id' => $langganan->id,
            ]
        );

        $this->assertSame(
            $billing->periode_mulai->toDateString(),
            $billing->fresh()->periode_mulai->toDateString()
        );

        $this->expectException(Throwable::class);

        TagihanBilling::create([
            'tenant_id' => $tenant->id,
            'langganan_id' => $langganan->id,
            'paket_langganan_id' => $paket->id,
            'nomor_tagihan' => 'TEST-DUPLICATE-DB-001',
            'tanggal_tagihan' => now()->toDateString(),
            'jatuh_tempo' => now()->addDays(7)->toDateString(),
            'periode_mulai' => $billing->periode_mulai->toDateString(),
            'periode_berakhir' => $billing->periode_berakhir->toDateString(),
            'subtotal' => $paket->harga,
            'diskon' => 0,
            'total' => $paket->harga,
            'status' => 'UNPAID',
            'dibayar_pada' => null,
            'catatan' => 'Test duplicate database constraint',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Package Validation
    |--------------------------------------------------------------------------
    */

    public function test_subscription_without_package_cannot_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        $langganan = new Langganan([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => 999999,
            'status' => 'active',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()
                ->subDays(30)
                ->toDateString(),
            'periode_berakhir' => now()
                ->addDays(3)
                ->toDateString(),
        ]);

        $langganan->exists = true;

        $this->expectException(\RuntimeException::class);

        $this->generateBilling($langganan);
    }

    /*
    |--------------------------------------------------------------------------
    | Billing Cycle
    |--------------------------------------------------------------------------
    */

    public function test_annual_package_generates_one_year_billing_period(): void
    {
        $tenant = $this->setupTenant();

        [
            'paket' => $paket,
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'tahunan',
            1200000
        );

        $billing = $this->generateBilling($langganan);

        $billingMulai = Carbon::parse(
            $langganan->periode_berakhir
        )->addDay();

        $this->assertSame(
            $billingMulai->toDateString(),
            $billing->periode_mulai->toDateString()
        );

        $billingBerakhir = $billingMulai
            ->copy()
            ->addYear()
            ->subDay();

        $this->assertSame(
            $billingBerakhir->toDateString(),
            $billing->periode_berakhir->toDateString()
        );

        $this->assertSame(
            (float) $paket->harga,
            (float) $billing->total
        );

        $this->assertSame(
            'tahunan',
            $paket->siklus_tagihan
        );

        $this->assertBasicBilling(
            $billing,
            $tenant,
            $langganan,
            $paket
        );
    }

    public function test_semester_package_generates_six_month_billing_period(): void
    {
        $tenant = $this->setupTenant();

        [
            'paket' => $paket,
            'langganan' => $langganan,
        ] = $this->createPackageSubscription(
            $tenant,
            'semester',
            750000
        );

        $billing = $this->generateBilling($langganan);

        /*
        * Billing dimulai sehari setelah
        * periode subscription berakhir.
        */
        $billingMulai = Carbon::parse(
            $langganan->periode_berakhir
        )->addDay();

        $this->assertSame(
            $billingMulai->toDateString(),
            $billing->periode_mulai->toDateString()
        );

        /*
        * Billing semester berlangsung
        * selama 6 bulan kurang 1 hari.
        */
        $billingBerakhir = $billingMulai
            ->copy()
            ->addMonths(6)
            ->subDay();

        $this->assertSame(
            $billingBerakhir->toDateString(),
            $billing->periode_berakhir->toDateString()
        );

        /*
        * Nominal billing harus sama
        * dengan harga paket.
        */
        $this->assertSame(
            (float) $paket->harga,
            (float) $billing->total
        );

        /*
        * Pastikan siklus paket benar.
        */
        $this->assertSame(
            'semester',
            $paket->siklus_tagihan
        );

        /*
        * Pastikan billing menggunakan
        * subscription yang benar.
        */
        $this->assertBasicBilling(
            $billing,
            $tenant,
            $langganan,
            $paket
        );
    }

    public function test_unsupported_billing_cycle_cannot_generate_billing(): void
    {
        $tenant = $this->setupTenant();

        /*
        * Buat package sebagai model in-memory.
        *
        * Jangan disimpan ke database karena database
        * memang melarang siklus billing yang tidak valid.
        */
        $paket = new PaketLangganan([
            'id' => 999999,
            'kode' => 'TEST-MINGGUAN',
            'nama' => 'Paket Mingguan Test',
            'deskripsi' => 'Paket khusus pengujian billing.',
            'harga' => 50000,
            'siklus_tagihan' => 'mingguan',
            'batas_siswa' => 300,
            'batas_pengguna' => 20,
            'batas_penyimpanan' => 5,
            'status' => true,
        ]);

        /*
        * Buat subscription sebagai model in-memory.
        */
        $langganan = new Langganan([
            'id' => 999999,
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => 'active',
            'mulai_pada' => now()->subDays(30),
            'trial_berakhir_pada' => null,
            'periode_mulai' => now()
                ->subDays(30)
                ->toDateString(),
            'periode_berakhir' => now()
                ->addDays(3)
                ->toDateString(),
            'dibatalkan_pada' => null,
        ]);

        /*
        * Tandai sebagai existing model tanpa INSERT.
        */
        $langganan->exists = true;

        /*
        * Inject relation package secara langsung.
        *
        * Ini membuat GenerateBillingService mendapatkan
        * package tanpa query database.
        */
        $langganan->setRelation('paket', $paket);

        /*
        * Service harus menolak siklus yang tidak didukung.
        */
        $this->expectException(\RuntimeException::class);

        $this->expectExceptionMessage(
            "Siklus billing 'mingguan' tidak didukung."
        );

        $this->generateBilling($langganan);
    }
}