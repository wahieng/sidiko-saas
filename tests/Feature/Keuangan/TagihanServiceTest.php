<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Models\Tenant;
use App\Core\Tenant\Context\TenantContext;
use App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Database\Seeders\DiskonPembayaranSeeder;
use Database\Seeders\JenisPembayaranSeeder;
use Database\Seeders\KelompokRombelSeeder;
use Database\Seeders\RombelSeeder;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\SiswaTahunSeeder;
use Database\Seeders\TarifPembayaranSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Database\Seeders\TenantSeeder;
use Database\Seeders\TagihanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanTest extends TestCase
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

        $this->seed([
            TahunAjaranSeeder::class,
            RombelSeeder::class,
            KelompokRombelSeeder::class,
            SiswaSeeder::class,
            SiswaTahunSeeder::class,
            JenisPembayaranSeeder::class,
            TarifPembayaranSeeder::class,
            DiskonPembayaranSeeder::class,
            TagihanSeeder::class,
        ]);
    }

    public function test_tagihan_can_be_created(): void
    {
        $siswaTahun = SiswaTahun::query()->firstOrFail();

        $tarif = TarifPembayaran::query()
            ->where('kelompok_rombel_id', $siswaTahun->kelompok_rombel_id)
            ->where('aktif', true)
            ->firstOrFail();

        $tagihan = Tagihan::query()->create([
            'tenant_id' => $this->tenant->id,
            'siswa_tahun_id' => $siswaTahun->id,
            'tarif_pembayaran_id' => $tarif->id,
            'nomor_tagihan' => 'TG/202607/000001',
            'nominal_awal' => $tarif->nominal,
            'tipe_diskon' => null,
            'nilai_diskon' => 0,
            'nominal_diskon' => 0,
            'nominal' => $tarif->nominal,
            'jumlah_dibayar' => 0,
            'sisa_tagihan' => $tarif->nominal,
            'tanggal_tagihan' => '2026-07-01',
            'tanggal_jatuh_tempo' => '2026-07-31',
            'status' => 'BELUM_BAYAR',
            'keterangan' => 'Test Tagihan',
        ]);

        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'tenant_id' => $this->tenant->id,
            'siswa_tahun_id' => $siswaTahun->id,
            'tarif_pembayaran_id' => $tarif->id,
        ]);
    }

    public function test_tagihan_has_siswa_tahun_relation(): void
    {
        $tagihan = Tagihan::query()->firstOrFail();

        $this->assertNotNull($tagihan->siswaTahun);
        $this->assertSame(
            $tagihan->siswa_tahun_id,
            $tagihan->siswaTahun->id
        );
    }

    public function test_tagihan_has_tarif_pembayaran_relation(): void
    {
        $tagihan = Tagihan::query()->first();

        if (! $tagihan) {
            $this->markTestSkipped('Belum ada data tagihan.');
        }

        $this->assertNotNull($tagihan->tarifPembayaran);
        $this->assertSame(
            $tagihan->tarif_pembayaran_id,
            $tagihan->tarifPembayaran->id
        );
    }

    public function test_tagihan_belongs_to_current_tenant(): void
    {
        $tagihan = Tagihan::query()->first();

        if (! $tagihan) {
            $this->markTestSkipped('Belum ada data tagihan.');
        }

        $this->assertSame(
            $this->tenant->id,
            $tagihan->tenant_id
        );
    }

    public function test_tagihan_can_be_updated(): void
    {
        $tagihan = Tagihan::query()->first();

        if (! $tagihan) {
            $this->markTestSkipped('Belum ada data tagihan.');
        }

        $tagihan->update([
            'keterangan' => 'Tagihan diperbarui',
        ]);

        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'keterangan' => 'Tagihan diperbarui',
        ]);
    }

    public function test_tagihan_can_be_cancelled(): void
    {
        $tagihan = Tagihan::query()->first();

        if (! $tagihan) {
            $this->markTestSkipped('Belum ada data tagihan.');
        }

        $tagihan->update([
            'status' => 'BATAL',
        ]);

        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'status' => 'BATAL',
        ]);
    }

    public function test_tagihan_cannot_be_seen_by_another_tenant(): void
    {
        $tagihan = Tagihan::query()->first();

        if (! $tagihan) {
            $this->markTestSkipped('Belum ada data tagihan.');
        }

        $otherTenant = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'other@school.test',
            'phone' => '0800000001',
            'address' => 'Other Address',
            'is_active' => true,
        ]);

        app(TenantContext::class)->set($otherTenant);

        $this->assertNull(
            Tagihan::query()->find($tagihan->id)
        );
    }
}