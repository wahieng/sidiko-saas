<?php

namespace Tests\Feature\Siswa;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use App\Modules\Siswa\SiswaTahun\Services\SiswaTahunService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaTahunTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /*
        |--------------------------------------------------------------------------
        | Seed seluruh data demo
        |--------------------------------------------------------------------------
        */

        $this->seed(DatabaseSeeder::class);

        /*
        |--------------------------------------------------------------------------
        | Pastikan Tenant Context tersedia
        |--------------------------------------------------------------------------
        */

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($tenant);
    }

    public function test_siswa_tahun_seeder_tersedia(): void
    {
        $this->assertGreaterThan(
            0,
            SiswaTahun::count()
        );
    }

    public function test_siswa_tahun_memiliki_relasi_siswa(): void
    {
        $data = SiswaTahun::with('siswa')->first();

        $this->assertNotNull($data);
        $this->assertNotNull($data->siswa);
    }

    public function test_siswa_tahun_memiliki_relasi_tahun_ajaran(): void
    {
        $data = SiswaTahun::with('tahunAjaran')->first();

        $this->assertNotNull($data);
        $this->assertNotNull($data->tahunAjaran);
    }

    public function test_siswa_tahun_memiliki_relasi_kelompok_rombel(): void
    {
        $data = SiswaTahun::with(
            'kelompokRombel.rombel'
        )->first();

        $this->assertNotNull($data);
        $this->assertNotNull($data->kelompokRombel);
        $this->assertNotNull(
            $data->kelompokRombel->rombel
        );
    }

    public function test_service_dapat_mengambil_siswa_aktif(): void
    {
        $data = SiswaTahun::first();

        $this->assertNotNull($data);

        $hasil = app(SiswaTahunService::class)
            ->aktifByTahunAjaran(
                $data->tahun_ajaran_id
            );

        $this->assertGreaterThan(
            0,
            $hasil->count()
        );

        $this->assertTrue(
            $hasil->every(
                fn ($item) => $item->status === 'AKTIF'
            )
        );
    }

    public function test_service_dapat_mengambil_riwayat_siswa(): void
    {
        $data = SiswaTahun::first();

        $this->assertNotNull($data);

        $hasil = app(SiswaTahunService::class)
            ->bySiswa($data->siswa_id);

        $this->assertGreaterThan(
            0,
            $hasil->count()
        );

        $this->assertTrue(
            $hasil->contains(
                'id',
                $data->id
            )
        );
    }
}