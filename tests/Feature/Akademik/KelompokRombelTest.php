<?php

namespace Tests\Feature\Akademik;

use App\Modules\Akademik\KelompokRombel\Models\KelompokRombel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelompokRombelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_kelompok_rombel_vii_tersedia(): void
    {
        $kelompok = KelompokRombel::where('nama', 'VII-A')->first();

        $this->assertNotNull($kelompok);
        $this->assertEquals('A', $kelompok->kode);
        $this->assertTrue($kelompok->aktif);
    }

    public function test_kelompok_rombel_viii_tersedia(): void
    {
        $kelompok = KelompokRombel::where('nama', 'VIII-A')->first();

        $this->assertNotNull($kelompok);
        $this->assertEquals('A', $kelompok->kode);
        $this->assertTrue($kelompok->aktif);
    }

    public function test_kelompok_rombel_ix_tersedia(): void
    {
        $kelompok = KelompokRombel::where('nama', 'IX-A')->first();

        $this->assertNotNull($kelompok);
        $this->assertEquals('A', $kelompok->kode);
        $this->assertTrue($kelompok->aktif);
    }

    public function test_kelompok_rombel_aktif_berjumlah_enam(): void
    {
        $kelompok = KelompokRombel::where('aktif', true)->get();

        $this->assertCount(6, $kelompok);
    }

    public function test_kelompok_rombel_terhubung_dengan_tahun_ajaran(): void
    {
        $kelompok = KelompokRombel::with('tahunAjaran')
            ->where('nama', 'VII-A')
            ->first();

        $this->assertNotNull($kelompok);
        $this->assertNotNull($kelompok->tahunAjaran);

        $this->assertEquals(
            '2026/2027',
            $kelompok->tahunAjaran->kode
        );
    }

    public function test_kelompok_rombel_terhubung_dengan_rombel(): void
    {
        $kelompok = KelompokRombel::with('rombel')
            ->where('nama', 'VII-A')
            ->first();

        $this->assertNotNull($kelompok);
        $this->assertNotNull($kelompok->rombel);

        $this->assertEquals(
            'VII',
            $kelompok->rombel->kode
        );
    }

    public function test_setiap_rombel_memiliki_dua_kelompok(): void
    {
        $rombelIds = KelompokRombel::query()
            ->select('rombel_id')
            ->distinct()
            ->pluck('rombel_id');

        $this->assertCount(3, $rombelIds);

        foreach ($rombelIds as $rombelId) {
            $jumlah = KelompokRombel::where(
                'rombel_id',
                $rombelId
            )->count();

            $this->assertEquals(2, $jumlah);
        }
    }
}