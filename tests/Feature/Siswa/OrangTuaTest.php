<?php

namespace Tests\Feature\Siswa;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\Models\OrangTua;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Siswa\Services\OrangTuaService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangTuaTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($this->tenant);
    }

    public function test_orang_tua_seeder_tersedia(): void
    {
        $this->assertGreaterThan(
            0,
            OrangTua::count()
        );
    }

    public function test_orang_tua_memiliki_tenant(): void
    {
        $orangTua = OrangTua::query()->first();

        $this->assertNotNull($orangTua);

        $this->assertEquals(
            $this->tenant->id,
            $orangTua->tenant_id
        );
    }

    public function test_orang_tua_memiliki_relasi_siswa(): void
    {
        $orangTua = OrangTua::with('siswa')->first();

        $this->assertNotNull($orangTua);
        $this->assertNotNull($orangTua->siswa);
    }

    public function test_siswa_memiliki_relasi_orang_tua(): void
    {
        $siswa = Siswa::with('orangTua')->first();

        $this->assertNotNull($siswa);

        $this->assertGreaterThan(
            0,
            $siswa->orangTua->count()
        );
    }

    public function test_service_dapat_mengambil_orang_tua_siswa(): void
    {
        $orangTua = OrangTua::query()->first();

        $this->assertNotNull($orangTua);

        $hasil = app(OrangTuaService::class)
            ->bySiswa($orangTua->siswa_id);

        $this->assertGreaterThan(
            0,
            $hasil->count()
        );

        $this->assertTrue(
            $hasil->contains(
                'id',
                $orangTua->id
            )
        );
    }

    public function test_service_dapat_mengambil_ayah(): void
    {
        $ayah = OrangTua::query()
            ->where('hubungan', 'AYAH')
            ->first();

        $this->assertNotNull($ayah);

        $hasil = app(OrangTuaService::class)
            ->ayah($ayah->siswa_id);

        $this->assertNotNull($hasil);
        $this->assertEquals(
            $ayah->id,
            $hasil->id
        );
    }

    public function test_service_dapat_mengambil_ibu(): void
    {
        $ibu = OrangTua::query()
            ->where('hubungan', 'IBU')
            ->first();

        $this->assertNotNull($ibu);

        $hasil = app(OrangTuaService::class)
            ->ibu($ibu->siswa_id);

        $this->assertNotNull($hasil);
        $this->assertEquals(
            $ibu->id,
            $hasil->id
        );
    }

    public function test_orang_tua_tidak_bisa_diakses_tanpa_tenant_context(): void
    {
        app(TenantContext::class)->clear();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        OrangTua::create([
            'siswa_id' => Siswa::query()
                ->withoutGlobalScopes()
                ->firstOrFail()
                ->id,
            'hubungan' => 'AYAH',
            'nama' => 'Test Tanpa Tenant',
        ]);
    }
}