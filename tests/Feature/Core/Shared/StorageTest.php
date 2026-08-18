<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Services\Storage\FileStorageService;
use App\Core\Shared\Support\Storage\StoragePathHelper;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Seed tenant
        $this->seed(TenantSeeder::class);

        // Ambil tenant DEMO
        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        // Aktifkan tenant context
        app(TenantContext::class)->set($this->tenant);
    }

    public function test_storage_path_menggunakan_tenant_aktif(): void
    {
        $path = app(StoragePathHelper::class)
            ->tenant();

        $this->assertEquals(
            'tenants/' . $this->tenant->id,
            $path
        );
    }

    public function test_path_foto_siswa_menggunakan_tenant(): void
    {
        $path = app(StoragePathHelper::class)
            ->siswaFoto();

        $this->assertEquals(
            'tenants/' . $this->tenant->id . '/siswa/foto',
            $path
        );
    }

    public function test_path_dokumen_siswa_menggunakan_tenant(): void
    {
        $path = app(StoragePathHelper::class)
            ->siswaDokumen();

        $this->assertEquals(
            'tenants/' . $this->tenant->id . '/siswa/dokumen',
            $path
        );
    }

    public function test_file_dapat_disimpan(): void
    {
        $service = app(FileStorageService::class);

        $path = 'tenants/' . $this->tenant->id
            . '/siswa/foto/test.txt';

        $hasil = $service->put(
            $path,
            'Test Storage SIDIKO'
        );

        $this->assertEquals(
            $path,
            $hasil
        );

        Storage::disk('public')->assertExists($path);
    }

    public function test_file_dapat_dibaca(): void
    {
        $service = app(FileStorageService::class);

        $path = 'tenants/' . $this->tenant->id
            . '/siswa/foto/test.txt';

        $service->put(
            $path,
            'Test Storage SIDIKO'
        );

        $isi = $service->get($path);

        $this->assertEquals(
            'Test Storage SIDIKO',
            $isi
        );
    }

    public function test_file_dapat_dicek_keberadaannya(): void
    {
        $service = app(FileStorageService::class);

        $path = 'tenants/' . $this->tenant->id
            . '/siswa/foto/test.txt';

        $this->assertFalse(
            $service->exists($path)
        );

        $service->put(
            $path,
            'Test Storage SIDIKO'
        );

        $this->assertTrue(
            $service->exists($path)
        );
    }

    public function test_file_dapat_dihapus(): void
    {
        $service = app(FileStorageService::class);

        $path = 'tenants/' . $this->tenant->id
            . '/siswa/foto/test.txt';

        $service->put(
            $path,
            'Test Storage SIDIKO'
        );

        $this->assertTrue(
            $service->exists($path)
        );

        $hasil = $service->delete($path);

        $this->assertTrue($hasil);

        $this->assertFalse(
            $service->exists($path)
        );
    }

    public function test_delete_file_yang_tidak_ada_menghasilkan_false(): void
    {
        $service = app(FileStorageService::class);

        $path = 'tenants/' . $this->tenant->id
            . '/siswa/foto/tidak-ada.txt';

        $this->assertFalse(
            $service->delete($path)
        );
    }

    public function test_file_memiliki_url(): void
    {
        $service = app(FileStorageService::class);

        $path = 'tenants/' . $this->tenant->id
            . '/siswa/foto/test.txt';

        $service->put(
            $path,
            'Test Storage SIDIKO'
        );

        $url = $service->url($path);

        $this->assertNotEmpty($url);
    }

    public function test_storage_path_ditolak_tanpa_tenant_context(): void
    {
        app(TenantContext::class)->clear();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        app(StoragePathHelper::class)->tenant();
    }
}