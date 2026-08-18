<?php

namespace Tests\Feature\Akademik;

use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahunAjaranTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_tahun_ajaran_seeder_tersedia(): void
    {
        $tahunAjaran = TahunAjaran::where(
            'kode',
            '2026/2027'
        )->first();

        $this->assertNotNull($tahunAjaran);

        $this->assertEquals(
            'Tahun Ajaran 2026/2027',
            $tahunAjaran->nama
        );

        $this->assertTrue($tahunAjaran->aktif);
    }

    public function test_tahun_ajaran_aktif_dapat_ditemukan(): void
    {
        $tahunAjaran = TahunAjaran::where(
            'aktif',
            true
        )->first();

        $this->assertNotNull($tahunAjaran);
    }

    public function test_tahun_ajaran_dapat_dinonaktifkan(): void
    {
        $tahunAjaran = TahunAjaran::where(
            'kode',
            '2026/2027'
        )->firstOrFail();

        $tahunAjaran->update([
            'aktif' => false,
        ]);

        $this->assertDatabaseHas('tahun_ajaran', [
            'id' => $tahunAjaran->id,
            'aktif' => false,
        ]);
    }
}