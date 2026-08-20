<?php

namespace Tests\Feature\Billings;

use App\Core\Billing\Models\TagihanBilling;
use App\Core\Billing\Services\BillingService;
use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected PaketLangganan $paket;

    protected Langganan $langganan;

    protected BillingService $billingService;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * Jalankan seeder utama SIDIKO.
         */
        $this->seed();

        /*
         * Ambil tenant DEMO.
         */
        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        /*
         * Aktifkan tenant context.
         */
        app(TenantContext::class)->set($this->tenant);

        /*
         * Buat paket khusus testing.
         */
        $this->paket = $this->createTestPackage();

        /*
         * Buat subscription default.
         */
        $this->langganan = $this->createSubscription(
            $this->tenant,
            $this->paket
        );

        /*
         * Resolve BillingService.
         */
        $this->billingService = app(BillingService::class);
    }

    protected function createTestPackage(
        string $siklus = 'bulanan',
        float $harga = 150000
    ): PaketLangganan {
        return PaketLangganan::create([
            'kode' => 'TEST-' . strtoupper($siklus),
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

    protected function createSubscription(
        Tenant $tenant,
        PaketLangganan $paket,
        string $status = 'active'
    ): Langganan {
        $periodeMulai = now()->subDays(30);
        $periodeBerakhir = now()->addDays(3);

        return Langganan::create([
            'tenant_id' => $tenant->id,
            'paket_langganan_id' => $paket->id,
            'status' => $status,
            'mulai_pada' => $periodeMulai,
            'trial_berakhir_pada' => null,
            'periode_mulai' => $periodeMulai->toDateString(),
            'periode_berakhir' => $periodeBerakhir->toDateString(),
            'dibatalkan_pada' => null,
        ]);
    }

    protected function createBillingData(
        array $overrides = []
    ): array {
        $periodeMulai = now()->addDay();

        $data = [
            'langganan_id' => $this->langganan->id,
            'paket_langganan_id' => $this->paket->id,

            'tanggal_tagihan' => now()->toDateString(),

            'jatuh_tempo' => now()
                ->addDays(7)
                ->toDateString(),

            'periode_mulai' => $periodeMulai->toDateString(),

            'periode_berakhir' => $periodeMulai
                ->copy()
                ->addMonth()
                ->subDay()
                ->toDateString(),

            'subtotal' => 150000,
            'diskon' => 0,
            'total' => 150000,

            'status' => 'UNPAID',

            'catatan' => null,
        ];

        return array_replace($data, $overrides);
    }

    protected function generateBilling(
        array $overrides = []
    ): TagihanBilling {
        return $this->billingService->create(
            $this->createBillingData($overrides)
        );
    }

    public function test_create_billing_successfully(): void
    {
        $billing = $this->generateBilling();

        $this->assertInstanceOf(
            TagihanBilling::class,
            $billing
        );

        $this->assertNotNull($billing->id);

        $this->assertSame(
            $this->langganan->id,
            $billing->langganan_id
        );

        $this->assertSame(
            $this->paket->id,
            $billing->paket_langganan_id
        );

        $this->assertNotNull(
            $billing->nomor_tagihan
        );

        $this->assertStringStartsWith(
            'BILL-',
            $billing->nomor_tagihan
        );

        $this->assertSame(
            'UNPAID',
            $billing->status
        );

        $this->assertSame(
            150000.0,
            (float) $billing->subtotal
        );

        $this->assertSame(
            0.0,
            (float) $billing->diskon
        );

        $this->assertSame(
            150000.0,
            (float) $billing->total
        );

        $this->assertDatabaseHas(
            'billing_tagihan',
            [
                'id' => $billing->id,
                'tenant_id' => $this->tenant->id,
                'langganan_id' => $this->langganan->id,
                'paket_langganan_id' => $this->paket->id,
                'status' => 'UNPAID',
            ]
        );
    }

    public function test_billing_generates_unique_invoice_number_automatically(): void
    {
        $billingPertama = $this->generateBilling();

        $billingKedua = $this->generateBilling([
            'periode_mulai' => now()
                ->addMonths(2)
                ->toDateString(),

            'periode_berakhir' => now()
                ->addMonths(3)
                ->subDay()
                ->toDateString(),
        ]);

        $this->assertNotEmpty(
            $billingPertama->nomor_tagihan
        );

        $this->assertNotEmpty(
            $billingKedua->nomor_tagihan
        );

        $this->assertStringStartsWith(
            'BILL-',
            $billingPertama->nomor_tagihan
        );

        $this->assertStringStartsWith(
            'BILL-',
            $billingKedua->nomor_tagihan
        );

        $this->assertNotSame(
            $billingPertama->nomor_tagihan,
            $billingKedua->nomor_tagihan
        );

        $this->assertDatabaseHas(
            'billing_tagihan',
            [
                'id' => $billingPertama->id,
                'nomor_tagihan' => $billingPertama->nomor_tagihan,
            ]
        );

        $this->assertDatabaseHas(
            'billing_tagihan',
            [
                'id' => $billingKedua->id,
                'nomor_tagihan' => $billingKedua->nomor_tagihan,
            ]
        );
    }

    public function test_update_billing_successfully(): void
    {
        $billing = $this->generateBilling();

        $updated = $this->billingService->update(
            $billing,
            [
                'jatuh_tempo' => now()
                    ->addDays(14)
                    ->toDateString(),

                'subtotal' => 200000,
                'diskon' => 10000,
                'total' => 190000,
                'catatan' => 'Billing diperbarui untuk pengujian.',
            ]
        );

        $this->assertInstanceOf(
            TagihanBilling::class,
            $updated
        );

        $this->assertSame(
            $billing->id,
            $updated->id
        );

        $this->assertSame(
            $this->langganan->id,
            $updated->langganan_id
        );

        $this->assertSame(
            $this->paket->id,
            $updated->paket_langganan_id
        );

        $this->assertSame(
            200000.0,
            (float) $updated->subtotal
        );

        $this->assertSame(
            10000.0,
            (float) $updated->diskon
        );

        $this->assertSame(
            190000.0,
            (float) $updated->total
        );

        $this->assertSame(
            'Billing diperbarui untuk pengujian.',
            $updated->catatan
        );

        $this->assertDatabaseHas(
            'billing_tagihan',
            [
                'id' => $billing->id,
                'tenant_id' => $this->tenant->id,
                'langganan_id' => $this->langganan->id,
                'paket_langganan_id' => $this->paket->id,
                'subtotal' => 200000,
                'diskon' => 10000,
                'total' => 190000,
                'status' => 'UNPAID',
                'catatan' => 'Billing diperbarui untuk pengujian.',
            ]
        );
    }
}