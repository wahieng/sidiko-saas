<?php

namespace Tests\Feature\Akademik;

use App\Modules\Akademik\Rombel\Models\Rombel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RombelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_rombel_vii_tersedia(): void
    {
        $rombel = Rombel::where('kode', 'VII')->first();

        $this->assertNotNull($rombel);
        $this->assertEquals('VII', $rombel->nama);
        $this->assertTrue($rombel->aktif);
    }

    public function test_rombel_viii_tersedia(): void
    {
        $rombel = Rombel::where('kode', 'VIII')->first();

        $this->assertNotNull($rombel);
        $this->assertEquals('VIII', $rombel->nama);
        $this->assertTrue($rombel->aktif);
    }

    public function test_rombel_ix_tersedia(): void
    {
        $rombel = Rombel::where('kode', 'IX')->first();

        $this->assertNotNull($rombel);
        $this->assertEquals('IX', $rombel->nama);
        $this->assertTrue($rombel->aktif);
    }

    public function test_rombel_aktif_berjumlah_tiga(): void
    {
        $rombel = Rombel::where('aktif', true)->get();

        $this->assertCount(3, $rombel);
    }

    public function test_rombel_tidak_bergantung_pada_tahun_ajaran(): void
    {
        $rombel = Rombel::first();

        $this->assertNotNull($rombel);

        $this->assertFalse(
            array_key_exists(
                'tahun_ajaran_id',
                $rombel->getAttributes()
            )
        );
    }
}