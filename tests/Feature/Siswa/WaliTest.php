<?php

namespace Tests\Feature\Siswa;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\Models\Wali;
use App\Modules\Siswa\Services\WaliService;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\TenantSeeder;
use Database\Seeders\WaliSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat tenant DEMO
        $this->seed(TenantSeeder::class);

        // Ambil tenant DEMO
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        // Aktifkan tenant context
        app(TenantContext::class)->set($tenant);

        // Seed siswa terlebih dahulu
        $this->seed(SiswaSeeder::class);

        // Seed wali
        $this->seed(WaliSeeder::class);
    }

    public function test_wali_seeder_tersedia(): void
    {
        $this->assertGreaterThan(
            0,
            Wali::count()
        );
    }

    public function test_wali_memiliki_tenant(): void
    {
        $wali = Wali::with('tenant')->first();

        $this->assertNotNull($wali);
        $this->assertNotNull($wali->tenant);

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $this->assertEquals(
            $tenant->id,
            $wali->tenant_id
        );
    }

    public function test_wali_memiliki_relasi_siswa(): void
    {
        $wali = Wali::with('siswa')->first();

        $this->assertNotNull($wali);
        $this->assertNotNull($wali->siswa);
    }

    public function test_siswa_memiliki_relasi_wali(): void
    {
        $wali = Wali::with('siswa')->first();

        $this->assertNotNull($wali);
        $this->assertNotNull($wali->siswa);

        $this->assertTrue(
            $wali->siswa->wali->contains(
                'id',
                $wali->id
            )
        );
    }

    public function test_service_dapat_mengambil_wali_siswa(): void
    {
        $wali = Wali::first();

        $this->assertNotNull($wali);

        $hasil = app(WaliService::class)
            ->bySiswa($wali->siswa_id);

        $this->assertGreaterThan(
            0,
            $hasil->count()
        );

        $this->assertTrue(
            $hasil->contains(
                'id',
                $wali->id
            )
        );
    }

    public function test_wali_bersifat_opsional(): void
    {
        $siswaTanpaWali = \App\Modules\Siswa\Models\Siswa::query()
            ->whereDoesntHave('wali')
            ->first();

        $this->assertNotNull($siswaTanpaWali);
        $this->assertCount(
            0,
            $siswaTanpaWali->wali
        );
    }
}