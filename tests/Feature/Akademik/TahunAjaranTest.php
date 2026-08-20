<?php

namespace Tests\Feature\Akademik;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahunAjaranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_tahun_ajaran_seeder_tersedia(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantContext = app(TenantContext::class);

        $tenantContext->set($tenant);

        $tahunAjaran = TahunAjaran::query()
            ->where('kode', '2026/2027')
            ->first();

        $this->assertNotNull($tahunAjaran);

        $this->assertSame(
            $tenant->id,
            $tahunAjaran->tenant_id
        );

        $this->assertSame(
            'Tahun Ajaran 2026/2027',
            $tahunAjaran->nama
        );

        $this->assertTrue($tahunAjaran->aktif);

        $tenantContext->clear();

        $this->assertFalse($tenantContext->has());
    }

    public function test_tahun_ajaran_aktif_dapat_ditemukan(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantContext = app(TenantContext::class);

        $tenantContext->set($tenant);

        $tahunAjaran = TahunAjaran::query()
            ->where('aktif', true)
            ->first();

        $this->assertNotNull($tahunAjaran);

        $this->assertSame(
            $tenant->id,
            $tahunAjaran->tenant_id
        );

        $tenantContext->clear();

        $this->assertFalse($tenantContext->has());
    }

    public function test_tahun_ajaran_dapat_dinonaktifkan(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $tenantContext = app(TenantContext::class);

        $tenantContext->set($tenant);

        $tahunAjaran = TahunAjaran::query()
            ->where('kode', '2026/2027')
            ->firstOrFail();

        $tahunAjaran->update([
            'aktif' => false,
        ]);

        $this->assertDatabaseHas('tahun_ajaran', [
            'id' => $tahunAjaran->id,
            'tenant_id' => $tenant->id,
            'aktif' => false,
        ]);

        $tenantContext->clear();

        $this->assertFalse($tenantContext->has());
    }

    public function test_tahun_ajaran_terisolasi_antar_tenant(): void
    {
        $tenantContext = app(TenantContext::class);

        $demo = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $other = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'admin@other-school.test',
            'phone' => '0811111111',
            'address' => 'Alamat Other School',
            'is_active' => true,
        ]);

        /*
         * Gunakan kode khusus test agar tidak bentrok
         * dengan data Tahun Ajaran dari seeder.
         *
         * Kedua tenant sengaja menggunakan kode yang sama.
         * Ini penting untuk membuktikan bahwa isolasi
         * benar-benar berasal dari tenant scope, bukan
         * dari perbedaan kode.
         */

        $tenantContext->set($demo);

        $tahunDemo = TahunAjaran::create([
            'tenant_id' => $demo->id,
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran DEMO 2099/2100',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        $tenantContext->set($other);

        $tahunOther = TahunAjaran::create([
            'tenant_id' => $other->id,
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran OTHER 2099/2100',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        /*
         * ==========================================
         * TENANT DEMO
         * ==========================================
         */

        $tenantContext->set($demo);

        $hasilDemo = TahunAjaran::query()
            ->where('kode', '2099/2100')
            ->get();

        $this->assertCount(1, $hasilDemo);

        $this->assertSame(
            $tahunDemo->id,
            $hasilDemo->first()->id
        );

        $this->assertNotSame(
            $tahunOther->id,
            $hasilDemo->first()->id
        );

        $this->assertSame(
            $demo->id,
            $hasilDemo->first()->tenant_id
        );

        /*
         * ==========================================
         * TENANT OTHER
         * ==========================================
         */

        $tenantContext->set($other);

        $hasilOther = TahunAjaran::query()
            ->where('kode', '2099/2100')
            ->get();

        $this->assertCount(1, $hasilOther);

        $this->assertSame(
            $tahunOther->id,
            $hasilOther->first()->id
        );

        $this->assertNotSame(
            $tahunDemo->id,
            $hasilOther->first()->id
        );

        $this->assertSame(
            $other->id,
            $hasilOther->first()->tenant_id
        );

        /*
         * ==========================================
         * CLEANUP
         * ==========================================
         */

        $tenantContext->clear();

        $this->assertFalse(
            $tenantContext->has()
        );
    }
}