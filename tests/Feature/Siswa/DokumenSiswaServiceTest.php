<?php

namespace Tests\Feature\Siswa;

use App\Core\Shared\Services\Storage\FileStorageService;
use App\Core\Shared\Support\Storage\StoragePathHelper;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\DokumenSiswa\Models\DokumenSiswa;
use App\Modules\Siswa\Siswa\Models\Siswa;
use App\Modules\Siswa\DokumenSiswa\Services\DokumenSiswaService;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class DokumenSiswaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected DokumenSiswaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->seed(TenantSeeder::class);

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)
            ->set($this->tenant);

        $this->seed(SiswaSeeder::class);

        $this->service = app(DokumenSiswaService::class);
    }

    public function test_dokumen_siswa_dapat_disimpan(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $file = UploadedFile::fake()->create(
            'kk.pdf',
            200,
            'application/pdf'
        );

        $dokumen = $this->service->store(
            $siswa,
            $file,
            'KARTU_KELUARGA',
            'Kartu keluarga siswa'
        );

        $this->assertInstanceOf(
            DokumenSiswa::class,
            $dokumen
        );

        $this->assertDatabaseHas(
            'dokumen_siswa',
            [
                'id' => $dokumen->id,
                'siswa_id' => $siswa->id,
                'jenis_dokumen' => 'KARTU_KELUARGA',
                'nama_asli' => 'kk.pdf',
            ]
        );
    }

    public function test_file_benar_benar_tersimpan_di_storage_core(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $file = UploadedFile::fake()->create(
            'ijazah.pdf',
            300,
            'application/pdf'
        );

        $dokumen = $this->service->store(
            $siswa,
            $file,
            'IJAZAH'
        );

        Storage::disk('public')->assertExists(
            $dokumen->path
        );
    }

    public function test_path_file_menggunakan_tenant_aktif(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $file = UploadedFile::fake()->create(
            'akta.pdf',
            150,
            'application/pdf'
        );

        $dokumen = $this->service->store(
            $siswa,
            $file,
            'AKTA_KELAHIRAN'
        );

        $this->assertStringStartsWith(
            'tenants/' . $this->tenant->id . '/siswa/dokumen/',
            $dokumen->path
        );
    }

    public function test_nama_file_disimpan_dengan_uuid(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $file = UploadedFile::fake()->create(
            'kk.pdf',
            100,
            'application/pdf'
        );

        $dokumen = $this->service->store(
            $siswa,
            $file,
            'KK'
        );

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f-]{36}\.pdf$/i',
            $dokumen->nama_file
        );
    }

    public function test_metadata_file_disimpan(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $file = UploadedFile::fake()->create(
            'kartu-keluarga.pdf',
            250,
            'application/pdf'
        );

        $dokumen = $this->service->store(
            $siswa,
            $file,
            'KARTU_KELUARGA',
            'Dokumen keluarga'
        );

        $this->assertEquals(
            'KARTU_KELUARGA',
            $dokumen->jenis_dokumen
        );

        $this->assertEquals(
            'kartu-keluarga.pdf',
            $dokumen->nama_asli
        );

        $this->assertEquals(
            'application/pdf',
            $dokumen->mime_type
        );

        $this->assertGreaterThan(
            0,
            $dokumen->ukuran
        );

        $this->assertEquals(
            'Dokumen keluarga',
            $dokumen->keterangan
        );

        $this->assertEquals(
            'public',
            $dokumen->disk
        );
    }

    public function test_siswa_tenant_lain_ditolak(): void
    {
        $tenantLain = Tenant::create([
            'name' => 'Tenant Lain',
            'code' => 'LAIN',
            'slug' => 'tenant-lain',
            'email' => 'lain@test.test',
            'phone' => '081234567890',
            'address' => 'Alamat Tenant Lain',
            'is_active' => true,
        ]);

        $siswaLainId = DB::table('siswa')->insertGetId([
            'tenant_id' => $tenantLain->id,
            'nis' => 'LAIN001',
            'nisn' => '9999999999',
            'nama' => 'Siswa Tenant Lain',
            'jenis_kelamin' => 'L',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $siswaLain = Siswa::withoutGlobalScopes()->findOrFail(
            $siswaLainId
        );

        $this->assertEquals(
            $tenantLain->id,
            $siswaLain->tenant_id
        );

        $this->assertNotEquals(
            $this->tenant->id,
            $siswaLain->tenant_id
        );

        $file = UploadedFile::fake()->create(
            'dokumen.pdf',
            100,
            'application/pdf'
        );

        $service = app(DokumenSiswaService::class);

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Siswa bukan milik tenant aktif.'
        );

        $service->store(
            $siswaLain,
            $file,
            'KK'
        );
    }

    public function test_tanpa_tenant_context_ditolak(): void
    {
        // Ambil siswa saat tenant context masih tersedia.
        $siswa = Siswa::query()->firstOrFail();

        // Setelah objek siswa didapat, hapus tenant context.
        app(TenantContext::class)->clear();

        $file = UploadedFile::fake()->create(
            'kk.pdf',
            100,
            'application/pdf'
        );

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'Tenant context tidak tersedia.'
        );

        $this->service->store(
            $siswa,
            $file,
            'KK'
        );
    }
}