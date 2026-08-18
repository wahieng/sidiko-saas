<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Services\Export\ExcelExportService;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromArray;
use Tests\TestCase;

class ExportTest extends TestCase
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

    public function test_excel_export_service_terdaftar(): void
    {
        $service = app(ExcelExportService::class);

        $this->assertInstanceOf(
            ExcelExportService::class,
            $service
        );
    }

    public function test_excel_dapat_diexport(): void
    {
        $data = [
            [
                'nis',
                'nama',
                'jenis_kelamin',
            ],
            [
                '10001',
                'Budi Santoso',
                'L',
            ],
            [
                '10002',
                'Siti Aminah',
                'P',
            ],
        ];

        $service = app(ExcelExportService::class);

        $path = $service->export(
            'siswa.xlsx',
            $data,
            [
                'directory' => 'exports',
                'disk' => 'local',
                'export_class' => TestExcelExport::class,
            ]
        );

        $this->assertEquals(
            'exports/siswa.xlsx',
            $path
        );

        $this->assertTrue(
            Storage::disk('local')->exists($path)
        );
    }

    public function test_excel_export_menghasilkan_file(): void
    {
        $data = [
            [
                'nis',
                'nama',
            ],
            [
                '10001',
                'Budi Santoso',
            ],
        ];

        $service = app(ExcelExportService::class);

        $path = $service->export(
            'test-siswa.xlsx',
            $data,
            [
                'export_class' => TestExcelExport::class,
            ]
        );

        $this->assertNotEmpty($path);

        $this->assertTrue(
            Storage::disk('local')->exists($path)
        );
    }

    public function test_export_excel_tanpa_export_class_ditolak(): void
    {
        $this->expectException(
            \App\Core\Shared\Exceptions\ExportException::class
        );

        app(ExcelExportService::class)->export(
            'siswa.xlsx',
            [
                ['nis', 'nama'],
                ['10001', 'Budi'],
            ]
        );
    }

    public function test_export_excel_dengan_nama_file_kosong_ditolak(): void
    {
        $this->expectException(
            \App\Core\Shared\Exceptions\ExportException::class
        );

        app(ExcelExportService::class)->export(
            '',
            [
                ['nis', 'nama'],
            ],
            [
                'export_class' => TestExcelExport::class,
            ]
        );
    }
}

/*
|--------------------------------------------------------------------------
| Test Excel Exporter
|--------------------------------------------------------------------------
*/

class TestExcelExport implements FromArray
{
    public function __construct(
        protected array $data
    ) {
    }

    public function array(): array
    {
        return $this->data;
    }
}