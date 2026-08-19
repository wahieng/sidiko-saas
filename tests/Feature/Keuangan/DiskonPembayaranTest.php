<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\Siswa\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiskonPembayaranTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected Siswa $siswa;
    protected TarifPembayaran $tarifPembayaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        $this->siswa = Siswa::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->tarifPembayaran = TarifPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();
    }

    public function test_diskon_pembayaran_tersedia(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->first();

        $this->assertNotNull($diskon);
    }

    public function test_diskon_terhubung_dengan_siswa(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotNull($diskon->siswa);
        $this->assertEquals(
            $diskon->siswa_id,
            $diskon->siswa->id
        );
    }

    public function test_diskon_terhubung_dengan_tarif_pembayaran(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotNull($diskon->tarifPembayaran);
        $this->assertEquals(
            $diskon->tarif_pembayaran_id,
            $diskon->tarifPembayaran->id
        );
    }

    public function test_diskon_persen_tersimpan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('tipe_diskon', 'PERSEN')
            ->first();

        $this->assertNotNull($diskon);
        $this->assertGreaterThan(0, $diskon->nilai);
        $this->assertLessThanOrEqual(100, $diskon->nilai);
    }

    public function test_diskon_nominal_tersimpan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('tipe_diskon', 'NOMINAL')
            ->first();

        $this->assertNotNull($diskon);
        $this->assertGreaterThan(0, $diskon->nilai);
    }

    public function test_keterangan_diskon_tersimpan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotEmpty($diskon->keterangan);
    }

    public function test_diskon_dapat_dinonaktifkan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $diskon->update([
            'aktif' => false,
        ]);

        $diskon->refresh();

        $this->assertFalse($diskon->aktif);
    }

    public function test_diskon_dapat_diaktifkan_kembali(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $diskon->update([
            'aktif' => false,
        ]);

        $diskon->update([
            'aktif' => true,
        ]);

        $diskon->refresh();

        $this->assertTrue($diskon->aktif);
    }

    public function test_diskon_memiliki_tanggal_berlaku(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->firstOrFail();

        $this->assertNotNull($diskon->tanggal_mulai);
        $this->assertNotNull($diskon->tanggal_selesai);

        $this->assertTrue(
            $diskon->tanggal_mulai->lte(
                $diskon->tanggal_selesai
            )
        );
    }

    public function test_diskon_siswa_dapat_ditemukan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('siswa_id', $this->siswa->id)
            ->first();

        $this->assertNotNull($diskon);
    }

    public function test_diskon_tidak_bercampur_antar_tenant(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->first();

        $this->assertNotNull($diskon);
        $this->assertEquals(
            $this->tenant->id,
            $diskon->tenant_id
        );
    }
}