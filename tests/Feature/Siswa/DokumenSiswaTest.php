<?php

namespace Tests\Feature\Siswa;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\Models\DokumenSiswa;
use App\Modules\Siswa\Models\Siswa;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokumenSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat tenant DEMO
        $this->seed(TenantSeeder::class);

        // Ambil tenant DEMO
        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        // Aktifkan tenant context
        app(TenantContext::class)->set($this->tenant);

        // Seed siswa
        $this->seed(SiswaSeeder::class);
    }

    public function test_dokumen_siswa_dapat_dibuat(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $dokumen = DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'AKTA_KELAHIRAN',
            'nama_file' => 'akta-kelahiran.pdf',
            'nama_asli' => 'Akta Kelahiran Budi.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/akta-kelahiran.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 125000,
            'keterangan' => 'Akta kelahiran siswa',
        ]);

        $this->assertNotNull($dokumen);

        $this->assertEquals(
            $this->tenant->id,
            $dokumen->tenant_id
        );

        $this->assertEquals(
            $siswa->id,
            $dokumen->siswa_id
        );
    }

    public function test_dokumen_memiliki_tenant(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $dokumen = DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'KK',
            'nama_file' => 'kk.pdf',
            'nama_asli' => 'Kartu Keluarga.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/kk.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 100000,
        ]);

        $this->assertNotNull($dokumen->tenant);

        $this->assertEquals(
            $this->tenant->id,
            $dokumen->tenant->id
        );
    }

    public function test_dokumen_memiliki_relasi_siswa(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $dokumen = DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'IJAZAH',
            'nama_file' => 'ijazah.pdf',
            'nama_asli' => 'Ijazah.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/ijazah.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 200000,
        ]);

        $this->assertNotNull($dokumen->siswa);

        $this->assertEquals(
            $siswa->id,
            $dokumen->siswa->id
        );
    }

    public function test_siswa_memiliki_relasi_dokumen(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $dokumen = DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'KIP',
            'nama_file' => 'kip.pdf',
            'nama_asli' => 'KIP.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/kip.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 150000,
        ]);

        // Refresh agar relasi mengambil data terbaru.
        $siswa->refresh();

        $this->assertTrue(
            $siswa->dokumen->contains(
                'id',
                $dokumen->id
            )
        );
    }

    public function test_dokumen_menyimpan_metadata_file(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $dokumen = DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'KARTU_KELUARGA',
            'nama_file' => 'kk-unik.pdf',
            'nama_asli' => 'Kartu Keluarga Budi.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/kk-unik.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 350000,
            'keterangan' => 'Dokumen keluarga',
        ]);

        $this->assertEquals(
            'KARTU_KELUARGA',
            $dokumen->jenis_dokumen
        );

        $this->assertEquals(
            'kk-unik.pdf',
            $dokumen->nama_file
        );

        $this->assertEquals(
            'Kartu Keluarga Budi.pdf',
            $dokumen->nama_asli
        );

        $this->assertEquals(
            'application/pdf',
            $dokumen->mime_type
        );

        $this->assertEquals(
            350000,
            $dokumen->ukuran
        );

        $this->assertEquals(
            'public',
            $dokumen->disk
        );

        $this->assertEquals(
            'Dokumen keluarga',
            $dokumen->keterangan
        );
    }

    public function test_dokumen_menggunakan_tenant_aktif(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        $dokumen = DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'KK',
            'nama_file' => 'kk.pdf',
            'nama_asli' => 'Kartu Keluarga.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/kk.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 100000,
        ]);

        $this->assertStringStartsWith(
            'tenants/' . $this->tenant->id . '/',
            $dokumen->path
        );

        $this->assertEquals(
            $this->tenant->id,
            $dokumen->tenant_id
        );
    }

    public function test_siswa_dapat_memiliki_banyak_jenis_dokumen(): void
    {
        $siswa = Siswa::query()->firstOrFail();

        DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'KK',
            'nama_file' => 'kk.pdf',
            'nama_asli' => 'KK.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/kk.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 100000,
        ]);

        DokumenSiswa::create([
            'siswa_id' => $siswa->id,
            'jenis_dokumen' => 'IJAZAH',
            'nama_file' => 'ijazah.pdf',
            'nama_asli' => 'Ijazah.pdf',
            'path' => 'tenants/' .
                $this->tenant->id .
                '/siswa/dokumen/ijazah.pdf',
            'disk' => 'public',
            'mime_type' => 'application/pdf',
            'ukuran' => 200000,
        ]);

        $this->assertEquals(
            2,
            DokumenSiswa::query()
                ->where('siswa_id', $siswa->id)
                ->count()
        );
    }
}