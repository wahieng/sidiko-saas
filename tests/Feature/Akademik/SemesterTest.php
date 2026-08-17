<?php

namespace Tests\Feature\Akademik;

use App\Modules\Akademik\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemesterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_semester_ganjil_tersedia(): void
    {
        $semester = Semester::where('kode', 'ganjil')->first();

        $this->assertNotNull($semester);

        $this->assertEquals(
            'Semester Ganjil',
            $semester->nama
        );

        $this->assertTrue($semester->aktif);
    }

    public function test_semester_genap_tersedia(): void
    {
        $semester = Semester::where('kode', 'genap')->first();

        $this->assertNotNull($semester);

        $this->assertEquals(
            'Semester Genap',
            $semester->nama
        );

        $this->assertFalse($semester->aktif);
    }

    public function test_semester_terhubung_dengan_tahun_ajaran(): void
    {
        $semester = Semester::with('tahunAjaran')
            ->where('kode', 'ganjil')
            ->first();

        $this->assertNotNull($semester);
        $this->assertNotNull($semester->tahunAjaran);

        $this->assertEquals(
            '2026/2027',
            $semester->tahunAjaran->kode
        );
    }

    public function test_tahun_ajaran_memiliki_dua_semester(): void
    {
        $tahunAjaran = \App\Modules\Akademik\Models\TahunAjaran::with('semesters')
            ->where('kode', '2026/2027')
            ->first();

        $this->assertNotNull($tahunAjaran);

        $this->assertCount(
            2,
            $tahunAjaran->semesters
        );
    }
}