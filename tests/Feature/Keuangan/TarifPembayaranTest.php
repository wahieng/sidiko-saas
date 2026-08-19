<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Core\Identity\Models\User;
use App\Modules\Akademik\KelompokRombel\Models\KelompokRombel;
use App\Modules\Akademik\Rombel\Models\Rombel;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use Database\Seeders\JenisPembayaranSeeder;
use Database\Seeders\KelompokRombelSeeder;
use Database\Seeders\RombelSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarifPembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected User $user;

    protected JenisPembayaran $jenisPembayaran;

    protected KelompokRombel $kelompokRombel;

    protected function setUp(): void
    {
        parent::setUp();

        /*
        |--------------------------------------------------------------------------
        | Tenant
        |--------------------------------------------------------------------------
        */

        $this->seed(TenantSeeder::class);

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($this->tenant);

        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Akademik
        |--------------------------------------------------------------------------
        */

        $this->seed(TahunAjaranSeeder::class);

        $this->seed(RombelSeeder::class);

        $this->seed(KelompokRombelSeeder::class);

        /*
        |--------------------------------------------------------------------------
        | Keuangan
        |--------------------------------------------------------------------------
        */

        $this->seed(JenisPembayaranSeeder::class);

        $this->jenisPembayaran = JenisPembayaran::query()
            ->where('kode', 'SPP')
            ->firstOrFail();

        $this->kelompokRombel = KelompokRombel::query()
            ->where('kode', 'A')
            ->whereHas('rombel', function ($query) {
                $query->where('kode', 'VII');
            })
            ->firstOrFail();
    }

    /*
    |--------------------------------------------------------------------------
    | Seeder / Relasi
    |--------------------------------------------------------------------------
    */

    public function test_tarif_pembayaran_dapat_dibuat(): void
    {
        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'keterangan' => 'Tarif SPP VII-A',
            'aktif' => true,
        ]);

        $this->assertDatabaseHas('tarif_pembayaran', [
            'id' => $tarif->id,
            'tenant_id' => $this->tenant->id,
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'aktif' => 1,
        ]);
    }

    public function test_tarif_pembayaran_memiliki_tenant(): void
    {
        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'aktif' => true,
        ]);

        $this->assertEquals(
            $this->tenant->id,
            $tarif->tenant_id
        );
    }

    public function test_tarif_pembayaran_memiliki_relasi_jenis_pembayaran(): void
    {
        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'aktif' => true,
        ]);

        $this->assertEquals(
            $this->jenisPembayaran->id,
            $tarif->jenisPembayaran->id
        );

        $this->assertEquals(
            'SPP',
            $tarif->jenisPembayaran->kode
        );
    }

    public function test_tarif_pembayaran_memiliki_relasi_kelompok_rombel(): void
    {
        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'aktif' => true,
        ]);

        $this->assertEquals(
            $this->kelompokRombel->id,
            $tarif->kelompok_rombel_id
        );

        $this->assertEquals(
            'A',
            $tarif->kelompokRombel->kode
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant Isolation
    |--------------------------------------------------------------------------
    */

    public function test_tarif_pembayaran_hanya_mengambil_tenant_aktif(): void
    {
        $tarifDemo = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'aktif' => true,
        ]);

        $tenantLain = Tenant::create([
            'name' => 'Sekolah Lain',
            'code' => 'LAIN',
            'slug' => 'sekolah-lain',
            'email' => 'admin@sekolah-lain.test',
            'phone' => '081234567890',
            'address' => 'Alamat Sekolah Lain',
            'is_active' => true,
        ]);

        app(TenantContext::class)->set($tenantLain);

        $jenisLain = JenisPembayaran::create([
            'kode' => 'SPP',
            'nama' => 'SPP Sekolah Lain',
            'kategori' => 'BULANAN',
            'aktif' => true,
        ]);

        /*
        | Kelompok Rombel untuk tenant lain
        */

        $tahunAjaranLain = TahunAjaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('kode', '2026/2027')
            ->firstOrFail();

        $rombelLain = Rombel::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('kode', 'VII')
            ->firstOrFail();

        $kelompokLain = KelompokRombel::withoutGlobalScopes()
            ->create([
                'tenant_id' => $tenantLain->id,
                'tahun_ajaran_id' => $tahunAjaranLain->id,
                'rombel_id' => $rombelLain->id,
                'kode' => 'A',
                'nama' => 'VII-A',
                'urutan' => 1,
                'aktif' => true,
            ]);

        TarifPembayaran::create([
            'jenis_pembayaran_id' => $jenisLain->id,
            'kelompok_rombel_id' => $kelompokLain->id,
            'nominal' => 200000,
            'aktif' => true,
        ]);

        /*
        | Kembali ke tenant DEMO
        */

        app(TenantContext::class)->set($this->tenant);

        $data = TarifPembayaran::all();

        $this->assertCount(1, $data);

        $this->assertEquals(
            $tarifDemo->id,
            $data->first()->id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_tarif_pembayaran_dapat_diperbarui(): void
    {
        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'keterangan' => 'Tarif lama',
            'aktif' => true,
        ]);

        $tarif->update([
            'nominal' => 175000,
            'keterangan' => 'Tarif baru',
        ]);

        $this->assertDatabaseHas('tarif_pembayaran', [
            'id' => $tarif->id,
            'nominal' => 175000,
            'keterangan' => 'Tarif baru',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    public function test_tarif_pembayaran_dapat_dinonaktifkan(): void
    {
        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'aktif' => true,
        ]);

        $tarif->update([
            'aktif' => false,
        ]);

        $this->assertDatabaseHas('tarif_pembayaran', [
            'id' => $tarif->id,
            'aktif' => 0,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function test_tarif_pembayaran_dapat_dihapus(): void
    {
        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'aktif' => true,
        ]);

        $id = $tarif->id;

        $tarif->delete();

        $this->assertDatabaseMissing(
            'tarif_pembayaran',
            [
                'id' => $id,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HTTP CRUD
    |--------------------------------------------------------------------------
    */

    public function test_guest_tidak_dapat_mengakses_tarif_pembayaran(): void
    {
        $response = $this->getJson('/tarif-pembayaran');

        $response->assertUnauthorized();
    }

    public function test_user_dapat_melihat_daftar_tarif_pembayaran(): void
    {
        $this->actingAs($this->user);

        TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'keterangan' => 'Tarif SPP VII-A',
            'aktif' => true,
        ]);

        $response = $this->getJson('/tarif-pembayaran');

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_user_dapat_membuat_tarif_melalui_http(): void
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/tarif-pembayaran', [
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'keterangan' => 'Tarif SPP VII-A',
            'aktif' => true,
        ]);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('tarif_pembayaran', [
            'tenant_id' => $this->tenant->id,
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
        ]);
    }

    public function test_user_dapat_melihat_detail_tarif_melalui_http(): void
    {
        $this->actingAs($this->user);

        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'keterangan' => 'Tarif SPP VII-A',
            'aktif' => true,
        ]);

        $response = $this->getJson(
            "/tarif-pembayaran/{$tarif->id}"
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'data',
            ]);
    }

    public function test_user_dapat_mengubah_tarif_melalui_http(): void
    {
        $this->actingAs($this->user);

        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'keterangan' => 'Tarif lama',
            'aktif' => true,
        ]);

        $response = $this->putJson(
            "/tarif-pembayaran/{$tarif->id}",
            [
                'jenis_pembayaran_id' => $this->jenisPembayaran->id,
                'kelompok_rombel_id' => $this->kelompokRombel->id,
                'nominal' => 175000,
                'keterangan' => 'Tarif baru',
                'aktif' => true,
            ]
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('tarif_pembayaran', [
            'id' => $tarif->id,
            'nominal' => 175000,
            'keterangan' => 'Tarif baru',
        ]);
    }

    public function test_user_dapat_menghapus_tarif_melalui_http(): void
    {
        $this->actingAs($this->user);

        $tarif = TarifPembayaran::create([
            'jenis_pembayaran_id' => $this->jenisPembayaran->id,
            'kelompok_rombel_id' => $this->kelompokRombel->id,
            'nominal' => 150000,
            'keterangan' => 'Tarif SPP VII-A',
            'aktif' => true,
        ]);

        $response = $this->deleteJson(
            "/tarif-pembayaran/{$tarif->id}"
        );

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('tarif_pembayaran', [
            'id' => $tarif->id,
        ]);
    }

    
}