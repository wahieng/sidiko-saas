<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use App\Modules\Keuangan\Tagihan\Services\GenerateTagihanService;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateTagihanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected SiswaTahun $siswaTahun;

    protected TarifPembayaran $tarifPembayaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($this->tenant);

        $this->siswaTahun = SiswaTahun::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('status', 'AKTIF')
            ->firstOrFail();

        $this->tarifPembayaran = TarifPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where(
                'kelompok_rombel_id',
                $this->siswaTahun->kelompok_rombel_id
            )
            ->where('aktif', true)
            ->firstOrFail();
    }

    public function test_generate_tagihan_tanpa_diskon(): void
    {
        DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where(
                'siswa_tahun_id',
                $this->siswaTahun->id
            )
            ->where(
                'tarif_pembayaran_id',
                $this->tarifPembayaran->id
            )
            ->update([
                'is_active' => false,
            ]);

        $tanggalTagihan = '2026-07-01';
        $tanggalJatuhTempo = '2026-07-31';

        $tagihan = app(GenerateTagihanService::class)->generate(
            $this->siswaTahun,
            $this->tarifPembayaran,
            $tanggalTagihan,
            $tanggalJatuhTempo
        );

        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'tenant_id' => $this->tenant->id,
            'siswa_tahun_id' => $this->siswaTahun->id,
            'tarif_pembayaran_id' => $this->tarifPembayaran->id,
            'nominal_awal' => $this->tarifPembayaran->nominal,
            'nominal_diskon' => 0,
            'nominal' => $this->tarifPembayaran->nominal,
            'jumlah_dibayar' => 0,
            'sisa_tagihan' => $this->tarifPembayaran->nominal,
            'status' => 'BELUM_BAYAR',
        ]);

        $this->assertStringStartsWith(
            'BILL-202607-',
            $tagihan->nomor_tagihan
        );
    }

    public function test_generate_tagihan_dengan_diskon_persen(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where(
                'siswa_tahun_id',
                $this->siswaTahun->id
            )
            ->where(
                'tarif_pembayaran_id',
                $this->tarifPembayaran->id
            )
            ->where('tipe_diskon', 'PERSEN')
            ->where('is_active', true)
            ->firstOrFail();

        $tanggalTagihan = '2026-07-01';

        $nominalAwal = (float) $this->tarifPembayaran->nominal;
        $nilaiDiskon = (float) $diskon->nilai;

        $nominalDiskon = min(
            $nominalAwal * ($nilaiDiskon / 100),
            $nominalAwal
        );

        $nominal = $nominalAwal - $nominalDiskon;

        $tagihan = app(GenerateTagihanService::class)->generate(
            $this->siswaTahun,
            $this->tarifPembayaran,
            $tanggalTagihan,
            '2026-07-31'
        );

        $this->assertSame(
            $diskon->tipe_diskon,
            $tagihan->tipe_diskon
        );

        $this->assertEquals(
            $nilaiDiskon,
            (float) $tagihan->nilai_diskon
        );

        $this->assertEquals(
            $nominalDiskon,
            (float) $tagihan->nominal_diskon
        );

        $this->assertEquals(
            $nominal,
            (float) $tagihan->nominal
        );

        $this->assertDatabaseHas('tagihan', [
            'id' => $tagihan->id,
            'tipe_diskon' => 'PERSEN',
            'nominal_diskon' => $nominalDiskon,
            'nominal' => $nominal,
        ]);
    }

    public function test_generate_tagihan_dengan_diskon_nominal(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('tipe_diskon', 'NOMINAL')
            ->where('is_active', true)
            ->firstOrFail();

        $siswaTahun = SiswaTahun::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('id', $diskon->siswa_tahun_id)
            ->where('status', 'AKTIF')
            ->firstOrFail();

        $tarif = TarifPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('id', $diskon->tarif_pembayaran_id)
            ->where('aktif', true)
            ->firstOrFail();

        $nominalAwal = (float) $tarif->nominal;
        $nominalDiskon = min(
            (float) $diskon->nilai,
            $nominalAwal
        );

        $nominal = $nominalAwal - $nominalDiskon;

        $tagihan = app(GenerateTagihanService::class)->generate(
            $siswaTahun,
            $tarif,
            '2026-07-01',
            '2026-07-31'
        );

        $this->assertSame(
            'NOMINAL',
            $tagihan->tipe_diskon
        );

        $this->assertEquals(
            $nominalDiskon,
            (float) $tagihan->nominal_diskon
        );

        $this->assertEquals(
            $nominal,
            (float) $tagihan->nominal
        );
    }

    public function test_diskon_tidak_aktif_tidak_digunakan(): void
    {
        $diskon = DiskonPembayaran::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where(
                'siswa_tahun_id',
                $this->siswaTahun->id
            )
            ->where(
                'tarif_pembayaran_id',
                $this->tarifPembayaran->id
            )
            ->first();

        if (! $diskon) {
            $this->markTestSkipped(
                'Belum ada diskon untuk siswa dan tarif tersebut.'
            );
        }

        $diskon->update([
            'is_active' => false,
        ]);

        $tagihan = app(GenerateTagihanService::class)->generate(
            $this->siswaTahun,
            $this->tarifPembayaran,
            '2026-07-01',
            '2026-07-31'
        );

        $this->assertNull($tagihan->tipe_diskon);
        $this->assertEquals(0, (float) $tagihan->nilai_diskon);
        $this->assertEquals(0, (float) $tagihan->nominal_diskon);
        $this->assertEquals(
            (float) $this->tarifPembayaran->nominal,
            (float) $tagihan->nominal
        );
    }

    public function test_nomor_tagihan_menggunakan_transaction_number_core(): void
    {
        $tagihan = app(GenerateTagihanService::class)->generate(
            $this->siswaTahun,
            $this->tarifPembayaran,
            '2026-07-01',
            '2026-07-31'
        );

        $this->assertMatchesRegularExpression(
            '/^BILL-202607-\d{6}$/',
            $tagihan->nomor_tagihan
        );
    }

    public function test_generate_for_all_hanya_siswa_aktif_dalam_kelompok_tarif(): void
    {
        $tagihan = app(GenerateTagihanService::class)->generateForAll(
            $this->tarifPembayaran,
            '2026-07-01',
            '2026-07-31'
        );

        $expectedCount = SiswaTahun::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('status', 'AKTIF')
            ->where(
                'kelompok_rombel_id',
                $this->tarifPembayaran->kelompok_rombel_id
            )
            ->count();

        $this->assertCount(
            $expectedCount,
            $tagihan
        );

        foreach ($tagihan as $item) {
            $this->assertSame(
                $this->tenant->id,
                $item->tenant_id
            );

            $this->assertSame(
                $this->tarifPembayaran->id,
                $item->tarif_pembayaran_id
            );

            $this->assertSame(
                'BELUM_BAYAR',
                $item->status
            );
        }
    }
}