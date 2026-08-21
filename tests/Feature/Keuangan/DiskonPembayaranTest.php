<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiskonPembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected SiswaTahun $siswaTahun;

    protected TarifPembayaran $tarifPembayaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $this->siswaTahun = SiswaTahun::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->tarifPembayaran = TarifPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();
    }

    public function test_diskon_pembayaran_tersedia(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->first();

        $this->assertNotNull($diskon);
    }

    public function test_diskon_terhubung_dengan_siswa_tahun(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotNull($diskon->siswaTahun);

        $this->assertEquals(
            $diskon->siswa_tahun_id,
            $diskon->siswaTahun->id
        );
    }

    public function test_diskon_terhubung_dengan_tarif_pembayaran(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotNull($diskon->tarifPembayaran);

        $this->assertEquals(
            $diskon->tarif_pembayaran_id,
            $diskon->tarifPembayaran->id
        );
    }

    public function test_diskon_persen_tersimpan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('tipe_diskon', 'PERSEN')
            ->first();

        $this->assertNotNull($diskon);
        $this->assertGreaterThan(0, $diskon->nilai);
        $this->assertLessThanOrEqual(100, $diskon->nilai);
    }

    public function test_diskon_nominal_tersimpan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('tipe_diskon', 'NOMINAL')
            ->first();

        $this->assertNotNull($diskon);
        $this->assertGreaterThan(0, $diskon->nilai);
    }

    public function test_keterangan_diskon_tersimpan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotEmpty($diskon->keterangan);
    }

    public function test_diskon_dapat_dinonaktifkan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $diskon->update([
            'is_active' => false,
        ]);

        $diskon->refresh();

        $this->assertFalse($diskon->is_active);
    }

    public function test_diskon_dapat_diaktifkan_kembali(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $diskon->update([
            'is_active' => false,
        ]);

        $diskon->update([
            'is_active' => true,
        ]);

        $diskon->refresh();

        $this->assertTrue($diskon->is_active);
    }

    public function test_diskon_memiliki_tanggal_berlaku(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotNull($diskon->tanggal_mulai);
        $this->assertNotNull($diskon->tanggal_selesai);

        $this->assertTrue(
            $diskon->tanggal_mulai->lte(
                $diskon->tanggal_selesai
            )
        );
    }

    public function test_diskon_siswa_tahun_dapat_ditemukan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where(
                'siswa_tahun_id',
                $this->siswaTahun->id
            )
            ->first();

        $this->assertNotNull($diskon);
    }

    public function test_diskon_tidak_bercampur_antar_tenant(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->first();

        $this->assertNotNull($diskon);

        $this->assertEquals(
            $this->tenant->id,
            $diskon->tenant_id
        );
    }

    public function test_diskon_tidak_dapat_menggunakan_siswa_tahun_dari_tenant_lain(): void
    {
        $tenantB = Tenant::query()->create([
            'name' => 'Tenant B',
            'code' => 'TENANT-B',
            'slug' => 'tenant-b',
            'email' => 'admin@tenant-b.test',
            'phone' => '081111111111',
            'address' => 'Alamat Tenant B',
            'is_active' => true,
        ]);

        $siswaTahunB = $this->siswaTahun->replicate();

        $siswaTahunB->tenant_id = $tenantB->id;

        $siswaTahunB->save();

        $context = app(TenantContext::class);

        $context->set($this->tenant);

        $diskon = DiskonPembayaran::create([
            'siswa_tahun_id' => $siswaTahunB->id,
            'tarif_pembayaran_id' => $this->tarifPembayaran->id,
            'tipe_diskon' => 'PERSEN',
            'nilai' => 10,
            'keterangan' => 'Cross tenant test',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()
                ->addMonth()
                ->toDateString(),
            'is_active' => true,
        ]);

        $diskon->refresh();

        $this->assertNotNull($diskon);

        $this->assertNull(
            $diskon->fresh()->siswaTahun
        );

        $context->clear();
    }

    public function test_diskon_tidak_dapat_menggunakan_tarif_dari_tenant_lain(): void
    {
        $tenantB = Tenant::query()->create([
            'name' => 'Tenant B',
            'code' => 'TENANT-B',
            'slug' => 'tenant-b',
            'email' => 'admin@tenant-b.test',
            'phone' => '081111111111',
            'address' => 'Alamat Tenant B',
            'is_active' => true,
        ]);

        $tarifB = $this->tarifPembayaran->replicate();

        $tarifB->tenant_id = $tenantB->id;

        $tarifB->save();

        $context = app(TenantContext::class);

        $context->set($this->tenant);

        $diskon = DiskonPembayaran::create([
            'siswa_tahun_id' => $this->siswaTahun->id,
            'tarif_pembayaran_id' => $tarifB->id,
            'tipe_diskon' => 'PERSEN',
            'nilai' => 10,
            'keterangan' => 'Cross tenant test',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()
                ->addMonth()
                ->toDateString(),
            'is_active' => true,
        ]);

        $diskon->refresh();

        $this->assertNotNull($diskon);

        $this->assertNull(
            $diskon->fresh()->tarifPembayaran
        );

        $context->clear();
    }
}