<?php

namespace Tests\Feature\Core\Tenant;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Core\Identity\Models\User;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CoreTenantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant Context
    |--------------------------------------------------------------------------
    */

    public function test_tenant_context_dapat_menetapkan_tenant(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $context = app(TenantContext::class);

        $context->set($tenant);

        $this->assertTrue(
            $context->has()
        );

        $this->assertSame(
            $tenant->id,
            $context->require()->id
        );

        $context->clear();

        $this->assertFalse(
            $context->has()
        );
    }

    public function test_tenant_context_require_gagal_jika_kosong(): void
    {
        $context = app(TenantContext::class);

        $context->clear();

        $this->assertFalse(
            $context->has()
        );

        $this->expectException(\RuntimeException::class);

        $this->expectExceptionMessage(
            'Tenant context belum tersedia.'
        );

        $context->require();
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant Scope
    |--------------------------------------------------------------------------
    */

    public function test_data_tenant_hanya_dapat_dilihat_oleh_tenant_aktif(): void
    {
        $demo = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $other = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'admin@other-school.test',
            'phone' => '0811111111',
            'address' => 'Alamat Other School',
            'is_active' => true,
        ]);

        $context = app(TenantContext::class);

        /*
         * Buat data milik DEMO.
         */
        $context->set($demo);

        $tahunDemo = TahunAjaran::create([
            'tenant_id' => $demo->id,
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran DEMO',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        /*
         * Buat data milik OTHER.
         */
        $context->set($other);

        $tahunOther = TahunAjaran::create([
            'tenant_id' => $other->id,
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran OTHER',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        /*
         * DEMO hanya boleh melihat DEMO.
         */
        $context->set($demo);

        $dataDemo = TahunAjaran::query()
            ->where('kode', '2099/2100')
            ->get();

        $this->assertCount(1, $dataDemo);

        $this->assertSame(
            $tahunDemo->id,
            $dataDemo->first()->id
        );

        $this->assertFalse(
            $dataDemo->contains(
                'id',
                $tahunOther->id
            )
        );

        /*
         * OTHER hanya boleh melihat OTHER.
         */
        $context->set($other);

        $dataOther = TahunAjaran::query()
            ->where('kode', '2099/2100')
            ->get();

        $this->assertCount(1, $dataOther);

        $this->assertSame(
            $tahunOther->id,
            $dataOther->first()->id
        );

        $this->assertFalse(
            $dataOther->contains(
                'id',
                $tahunDemo->id
            )
        );

        $context->clear();

        $this->assertFalse(
            $context->has()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Tenant Auto Fill
    |--------------------------------------------------------------------------
    */

    public function test_tenant_id_diisi_otomatis_dari_context(): void
    {
        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $context = app(TenantContext::class);

        $context->set($tenant);

        $tahun = TahunAjaran::create([
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran Test',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        $this->assertSame(
            $tenant->id,
            $tahun->tenant_id
        );

        $this->assertDatabaseHas('tahun_ajaran', [
            'id' => $tahun->id,
            'tenant_id' => $tenant->id,
        ]);

        $context->clear();
    }

    /*
    |--------------------------------------------------------------------------
    | Fail Closed
    |--------------------------------------------------------------------------
    */

    public function test_data_tenant_tidak_dapat_dibuat_tanpa_context(): void
    {
        $context = app(TenantContext::class);

        $context->clear();

        $this->assertFalse(
            $context->has()
        );

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        TahunAjaran::create([
            'kode' => '2099/2100',
            'nama' => 'Tanpa Tenant',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    */

    public function test_tenant_middleware_mengisi_context_dari_user(): void
    {
        Route::middleware(['auth', 'tenant'])
            ->get('/__test/core-tenant', function () {
                $context = app(TenantContext::class);

                return response()->json([
                    'tenant_id' => $context->require()->id,
                    'has_context' => $context->has(),
                ]);
            });

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        /*
        * Cari user yang memang terikat
        * dengan tenant DEMO.
        */
        $user = User::query()
            ->where('tenant_id', $tenant->id)
            ->first();

        $this->assertNotNull(
            $user,
            'User tenant DEMO belum tersedia.'
        );

        /*
        * Request login sebagai user tenant.
        *
        * TenantMiddleware menggunakan tenant_id
        * dari authenticated user untuk mengisi
        * TenantContext.
        */
        $response = $this
            ->actingAs($user)
            ->get('/__test/core-tenant');

        $response->assertOk();

        $response->assertJson([
            'tenant_id' => $tenant->id,
            'has_context' => true,
        ]);

        /*
        * Pastikan context memang menunjuk
        * ke tenant yang benar.
        */
        $this->assertSame(
            $tenant->id,
            $response->json('tenant_id')
        );

        /*
        * Middleware seharusnya meninggalkan context
        * selama request berlangsung.
        */
        $this->assertTrue(
            $response->json('has_context')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Context Cleanup
    |--------------------------------------------------------------------------
    */

    public function test_context_dapat_dibersihkan_setelah_request(): void
    {
        $context = app(TenantContext::class);

        $tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $context->set($tenant);

        $this->assertTrue(
            $context->has()
        );

        $context->clear();

        $this->assertFalse(
            $context->has()
        );
    }

    public function test_tenant_aktif_tidak_dapat_menemukan_data_tenant_lain(): void
    {
        $demo = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $other = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'admin@other-school.test',
            'phone' => '0811111111',
            'address' => 'Alamat Other School',
            'is_active' => true,
        ]);

        $context = app(TenantContext::class);

        $context->set($other);

        $tahunOther = TahunAjaran::create([
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran OTHER',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        $context->set($demo);

        $this->assertNull(
            TahunAjaran::query()->find($tahunOther->id)
        );

        $context->clear();
    }

    public function test_tenant_aktif_tidak_dapat_mengubah_data_tenant_lain(): void
    {
        $demo = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $other = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'admin@other-school.test',
            'phone' => '0811111111',
            'address' => 'Alamat Other School',
            'is_active' => true,
        ]);

        $context = app(TenantContext::class);

        $context->set($other);

        $tahunOther = TahunAjaran::create([
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran OTHER',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        $context->set($demo);

        $updated = TahunAjaran::query()
            ->whereKey($tahunOther->id)
            ->update([
                'nama' => 'DIUBAH OLEH DEMO',
            ]);

        $this->assertSame(0, $updated);

        $context->set($other);

        $this->assertSame(
            'Tahun Ajaran OTHER',
            TahunAjaran::query()
                ->findOrFail($tahunOther->id)
                ->nama
        );

        $context->clear();
    }

    public function test_tenant_aktif_tidak_dapat_menghapus_data_tenant_lain(): void
    {
        $demo = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $other = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'admin@other-school.test',
            'phone' => '0811111111',
            'address' => 'Alamat Other School',
            'is_active' => true,
        ]);

        $context = app(TenantContext::class);

        $context->set($other);

        $tahunOther = TahunAjaran::create([
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran OTHER',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        $context->set($demo);

        $deleted = TahunAjaran::query()
            ->whereKey($tahunOther->id)
            ->delete();

        $this->assertSame(0, $deleted);

        $this->assertDatabaseHas('tahun_ajaran', [
            'id' => $tahunOther->id,
            'tenant_id' => $other->id,
        ]);

        $context->clear();
    }

    public function test_context_dapat_berpindah_tenant_tanpa_mencampur_data(): void
    {
        $demo = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $other = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'admin@other-school.test',
            'phone' => '0811111111',
            'address' => 'Alamat Other School',
            'is_active' => true,
        ]);

        $context = app(TenantContext::class);

        $context->set($demo);

        $tahunDemo = TahunAjaran::create([
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran DEMO',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        $context->set($other);

        $tahunOther = TahunAjaran::create([
            'kode' => '2099/2100',
            'nama' => 'Tahun Ajaran OTHER',
            'tanggal_mulai' => '2099-07-01',
            'tanggal_selesai' => '2100-06-30',
            'aktif' => true,
        ]);

        $this->assertSame(
            $tahunOther->id,
            TahunAjaran::query()
                ->where('kode', '2099/2100')
                ->firstOrFail()
                ->id
        );

        $context->set($demo);

        $this->assertSame(
            $tahunDemo->id,
            TahunAjaran::query()
                ->where('kode', '2099/2100')
                ->firstOrFail()
                ->id
        );

        $context->clear();
    }
}