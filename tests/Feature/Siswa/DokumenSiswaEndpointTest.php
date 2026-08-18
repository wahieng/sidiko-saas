<?php

namespace Tests\Feature\Siswa;

use App\Core\Identity\Models\User;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\DokumenSiswa\Models\DokumenSiswa;
use App\Modules\Siswa\Siswa\Models\Siswa;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenSiswaEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected User $user;

    protected Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // =========================================================
        // TENANT
        // =========================================================

        $this->seed(TenantSeeder::class);

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        // Aktifkan tenant context
        app(TenantContext::class)->set($this->tenant);

        // =========================================================
        // SISWA
        // =========================================================

        $this->seed(SiswaSeeder::class);

        $this->siswa = Siswa::query()
            ->withoutGlobalScopes()
            ->firstOrFail();

        // Pastikan tenant_id benar-benar tersimpan
        // forceFill digunakan agar tidak terhalang $fillable
        $this->siswa->forceFill([
            'tenant_id' => $this->tenant->id,
        ])->save();

        // Ambil ulang dari database
        $this->siswa->refresh();

        // =========================================================
        // USER
        // =========================================================

        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($this->user);
    }

    public function test_endpoint_dapat_mengupload_dokumen_siswa(): void
    {
        $file = UploadedFile::fake()->create(
            'kartu-keluarga.pdf',
            200,
            'application/pdf'
        );

        $response = $this->post(
            '/siswa/dokumen',
            [
                'siswa_id' => $this->siswa->id,
                'jenis_dokumen' => 'KARTU_KELUARGA',
                'file' => $file,
                'keterangan' => 'Kartu keluarga siswa',
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas(
            'dokumen_siswa',
            [
                'siswa_id' => $this->siswa->id,
                'jenis_dokumen' => 'KARTU_KELUARGA',
                'nama_asli' => 'kartu-keluarga.pdf',
                'tenant_id' => $this->tenant->id,
            ]
        );

        $dokumen = DokumenSiswa::query()
            ->where('siswa_id', $this->siswa->id)
            ->where('jenis_dokumen', 'KARTU_KELUARGA')
            ->latest('id')
            ->firstOrFail();

        Storage::disk('public')->assertExists(
            $dokumen->path
        );
    }

    public function test_endpoint_menolak_file_tidak_valid(): void
    {
        $file = UploadedFile::fake()->create(
            'dokumen.txt',
            100,
            'text/plain'
        );

        $response = $this->post(
            '/siswa/dokumen',
            [
                'siswa_id' => $this->siswa->id,
                'jenis_dokumen' => 'KK',
                'file' => $file,
            ]
        );

        $response->assertSessionHasErrors('file');

        $this->assertDatabaseCount(
            'dokumen_siswa',
            0
        );
    }

    public function test_endpoint_menolak_file_lebih_dari_10_mb(): void
    {
        $file = UploadedFile::fake()->create(
            'dokumen.pdf',
            11000,
            'application/pdf'
        );

        $response = $this->post(
            '/siswa/dokumen',
            [
                'siswa_id' => $this->siswa->id,
                'jenis_dokumen' => 'KK',
                'file' => $file,
            ]
        );

        $response->assertSessionHasErrors('file');

        $this->assertDatabaseCount(
            'dokumen_siswa',
            0
        );
    }

    public function test_endpoint_menolak_siswa_tidak_valid(): void
    {
        $file = UploadedFile::fake()->create(
            'kk.pdf',
            100,
            'application/pdf'
        );

        $response = $this->post(
            '/siswa/dokumen',
            [
                'siswa_id' => 999999,
                'jenis_dokumen' => 'KK',
                'file' => $file,
            ]
        );

        $response->assertSessionHasErrors('siswa_id');

        $this->assertDatabaseCount(
            'dokumen_siswa',
            0
        );
    }

    public function test_endpoint_menolak_jenis_dokumen_kosong(): void
    {
        $file = UploadedFile::fake()->create(
            'kk.pdf',
            100,
            'application/pdf'
        );

        $response = $this->post(
            '/siswa/dokumen',
            [
                'siswa_id' => $this->siswa->id,
                'file' => $file,
            ]
        );

        $response->assertSessionHasErrors(
            'jenis_dokumen'
        );

        $this->assertDatabaseCount(
            'dokumen_siswa',
            0
        );
    }

    public function test_endpoint_menyimpan_metadata_dokumen(): void
    {
        $file = UploadedFile::fake()->create(
            'akta.pdf',
            150,
            'application/pdf'
        );

        $response = $this->post(
            '/siswa/dokumen',
            [
                'siswa_id' => $this->siswa->id,
                'jenis_dokumen' => 'AKTA_KELAHIRAN',
                'file' => $file,
                'keterangan' => 'Akta kelahiran siswa',
            ]
        );

        $response->assertRedirect();

        $dokumen = DokumenSiswa::query()
            ->where('siswa_id', $this->siswa->id)
            ->where(
                'jenis_dokumen',
                'AKTA_KELAHIRAN'
            )
            ->firstOrFail();

        $this->assertEquals(
            'akta.pdf',
            $dokumen->nama_asli
        );

        $this->assertEquals(
            'application/pdf',
            $dokumen->mime_type
        );

        $this->assertEquals(
            'Akta kelahiran siswa',
            $dokumen->keterangan
        );

        $this->assertEquals(
            $this->tenant->id,
            $dokumen->tenant_id
        );

        $this->assertNotEmpty(
            $dokumen->path
        );

        $this->assertNotEmpty(
            $dokumen->nama_file
        );

        Storage::disk('public')->assertExists(
            $dokumen->path
        );
    }
}