<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Exceptions\PdfException;
use App\Core\Shared\Services\Pdf\PdfGeneratorService;
use App\Core\Shared\Support\Pdf\PdfHelper;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->seed(TenantSeeder::class);

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($this->tenant);
    }

    public function test_pdf_generator_service_terdaftar(): void
    {
        $service = app(PdfGeneratorService::class);

        $this->assertInstanceOf(
            PdfGeneratorService::class,
            $service
        );
    }

    public function test_filename_pdf_dapat_dinormalisasi(): void
    {
        $this->assertEquals(
            'siswa.pdf',
            PdfHelper::normalizeFilename('siswa')
        );

        $this->assertEquals(
            'siswa.pdf',
            PdfHelper::normalizeFilename('siswa.pdf')
        );
    }

    public function test_pdf_dapat_dibuat(): void
    {
        $service = app(PdfGeneratorService::class);

        $path = $service->generate(
            'test.pdf',
            'pdf.test',
            [
                'title' => 'Test PDF SIDIKO',
                'message' => 'PDF berhasil dibuat.',
            ],
            [
                'disk' => 'local',
                'directory' => 'exports',
            ]
        );

        $this->assertEquals(
            'exports/test.pdf',
            $path
        );

        $this->assertTrue(
            Storage::disk('local')->exists($path)
        );
    }

    public function test_pdf_menghasilkan_file_yang_tidak_kosong(): void
    {
        $service = app(PdfGeneratorService::class);

        $path = $service->generate(
            'test-content.pdf',
            'pdf.test',
            [
                'title' => 'SIDIKO',
                'message' => 'Testing PDF.',
            ]
        );

        $content = Storage::disk('local')->get($path);

        $this->assertNotEmpty($content);

        $this->assertStringStartsWith(
            '%PDF',
            $content
        );
    }

    public function test_view_pdf_yang_tidak_ada_ditolak(): void
    {
        $this->expectException(PdfException::class);

        app(PdfGeneratorService::class)->generate(
            'error.pdf',
            'pdf.view-tidak-ada'
        );
    }

    public function test_nama_file_kosong_ditolak(): void
    {
        $this->expectException(PdfException::class);

        app(PdfGeneratorService::class)->generate(
            '',
            'pdf.test'
        );
    }
}