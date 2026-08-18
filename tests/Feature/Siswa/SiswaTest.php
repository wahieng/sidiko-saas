<?php

namespace Tests\Feature\Siswa;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\Siswa\Models\Siswa;
use App\Modules\Siswa\Siswa\Services\SiswaService;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaTest extends TestCase
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

        // Seed data siswa
        $this->seed(SiswaSeeder::class);
    }

    public function test_siswa_seeder_tersedia(): void
    {
        $this->assertGreaterThan(
            0,
            Siswa::count()
        );
    }

    public function test_siswa_dapat_dicari_dengan_nis(): void
    {
        $siswa = Siswa::query()->first();

        $this->assertNotNull($siswa);

        $hasil = app(SiswaService::class)
            ->findByNis($siswa->nis);

        $this->assertNotNull($hasil);

        $this->assertEquals(
            $siswa->id,
            $hasil->id
        );
    }

    public function test_service_dapat_mengambil_semua_siswa(): void
    {
        $hasil = app(SiswaService::class)->all();

        $this->assertCount(
            Siswa::count(),
            $hasil
        );
    }

    public function test_siswa_memiliki_data_identitas(): void
    {
        $siswa = Siswa::query()->first();

        $this->assertNotNull($siswa);
        $this->assertNotEmpty($siswa->nis);
        $this->assertNotEmpty($siswa->nama);
    }

    public function test_foto_siswa_merupakan_field_identitas(): void
    {
        $siswa = Siswa::query()->first();

        $this->assertNotNull($siswa);

        $this->assertTrue(
            array_key_exists(
                'foto',
                $siswa->getAttributes()
            )
        );
    }
}