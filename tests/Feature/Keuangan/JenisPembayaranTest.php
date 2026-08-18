<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran;
use Database\Seeders\JenisPembayaranSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JenisPembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TenantSeeder::class);

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($this->tenant);
    }

    public function test_seeder_jenis_pembayaran_tersedia(): void
    {
        $this->seed(JenisPembayaranSeeder::class);

        $this->assertDatabaseCount(
            'jenis_pembayaran',
            5
        );
    }

    public function test_jenis_pembayaran_memiliki_tenant(): void
    {
        $this->seed(JenisPembayaranSeeder::class);

        $jenis = JenisPembayaran::query()
            ->where('kode', 'SPP')
            ->firstOrFail();

        $this->assertEquals(
            $this->tenant->id,
            $jenis->tenant_id
        );

        $this->assertEquals(
            $this->tenant->id,
            $jenis->tenant->id
        );
    }

    public function test_jenis_pembayaran_dapat_dibuat(): void
    {
        $jenis = JenisPembayaran::create([
            'kode' => 'DSP',
            'nama' => 'Dana Sumbangan Pendidikan',
            'kategori' => 'TAHUNAN',
            'keterangan' => 'Pembayaran dana pendidikan.',
            'aktif' => true,
        ]);

        $this->assertDatabaseHas('jenis_pembayaran', [
            'id' => $jenis->id,
            'tenant_id' => $this->tenant->id,
            'kode' => 'DSP',
            'nama' => 'Dana Sumbangan Pendidikan',
            'kategori' => 'TAHUNAN',
            'aktif' => 1,
        ]);
    }

    public function test_jenis_pembayaran_hanya_mengambil_tenant_aktif(): void
    {
        $tenantLain = Tenant::create([
            'name' => 'Sekolah Lain',
            'code' => 'LAIN',
            'slug' => 'sekolah-lain',
            'email' => 'admin@sekolah-lain.test',
            'phone' => '081234567890',
            'address' => 'Alamat Sekolah Lain',
            'is_active' => true,
        ]);

        JenisPembayaran::create([
            'kode' => 'SPP',
            'nama' => 'SPP Sekolah Demo',
            'kategori' => 'BULANAN',
            'aktif' => true,
        ]);

        app(TenantContext::class)->set($tenantLain);

        JenisPembayaran::create([
            'kode' => 'SPP',
            'nama' => 'SPP Sekolah Lain',
            'kategori' => 'BULANAN',
            'aktif' => true,
        ]);

        app(TenantContext::class)->set($this->tenant);

        $data = JenisPembayaran::all();

        $this->assertCount(1, $data);
        $this->assertEquals(
            'SPP Sekolah Demo',
            $data->first()->nama
        );
    }

    public function test_jenis_pembayaran_dapat_diperbarui(): void
    {
        $jenis = JenisPembayaran::create([
            'kode' => 'SPP',
            'nama' => 'SPP Lama',
            'kategori' => 'BULANAN',
            'aktif' => true,
        ]);

        $jenis->update([
            'nama' => 'SPP Bulanan',
        ]);

        $this->assertDatabaseHas('jenis_pembayaran', [
            'id' => $jenis->id,
            'nama' => 'SPP Bulanan',
        ]);
    }

    public function test_jenis_pembayaran_dapat_dinonaktifkan(): void
    {
        $jenis = JenisPembayaran::create([
            'kode' => 'SPP',
            'nama' => 'SPP Bulanan',
            'kategori' => 'BULANAN',
            'aktif' => true,
        ]);

        $jenis->update([
            'aktif' => false,
        ]);

        $this->assertDatabaseHas('jenis_pembayaran', [
            'id' => $jenis->id,
            'aktif' => 0,
        ]);
    }

    public function test_jenis_pembayaran_dapat_dihapus(): void
    {
        $jenis = JenisPembayaran::create([
            'kode' => 'TEMP',
            'nama' => 'Pembayaran Sementara',
            'kategori' => 'SEKALI',
            'aktif' => true,
        ]);

        $id = $jenis->id;

        $jenis->delete();

        $this->assertDatabaseMissing(
            'jenis_pembayaran',
            [
                'id' => $id,
            ]
        );
    }
}