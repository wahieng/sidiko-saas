<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Services\Import\CsvImportService;
use App\Core\Shared\Support\Excel\ImportHelper;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CsvImportTest extends TestCase
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

    public function test_file_csv_dapat_divalidasi(): void
    {
        $file = UploadedFile::fake()->create(
            'siswa.csv',
            100,
            'text/csv'
        );

        ImportHelper::validateFile($file);

        $this->assertTrue(
            ImportHelper::isCsv($file)
        );

        $this->assertEquals(
            'csv',
            ImportHelper::extension($file)
        );
    }

    public function test_file_excel_bukan_csv(): void
    {
        $file = UploadedFile::fake()->create(
            'siswa.xlsx',
            100,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        ImportHelper::validateFile($file);

        $this->assertTrue(
            ImportHelper::isExcel($file)
        );

        $this->assertFalse(
            ImportHelper::isCsv($file)
        );

        $this->assertEquals(
            'xlsx',
            ImportHelper::extension($file)
        );
    }

    public function test_file_csv_dapat_dibaca(): void
    {
        $path = 'test/import/siswa.csv';

        Storage::disk('local')->put(
            $path,
            "nis,nama,jenis_kelamin\n" .
            "10001,Budi Santoso,L\n" .
            "10002,Siti Aminah,P\n"
        );

        $this->assertTrue(
            Storage::disk('local')->exists($path)
        );

        $content = Storage::disk('local')->get($path);

        $this->assertStringContainsString(
            'Budi Santoso',
            $content
        );

        $this->assertStringContainsString(
            'Siti Aminah',
            $content
        );
    }

    public function test_csv_import_service_terdaftar(): void
    {
        $service = app(CsvImportService::class);

        $this->assertInstanceOf(
            CsvImportService::class,
            $service
        );
    }

    public function test_csv_sungguhan_dapat_dibaca_dan_diproses(): void
    {
        $path = tempnam(
            sys_get_temp_dir(),
            'sidiko_csv_'
        );

        $this->assertNotFalse($path);

        try {
            file_put_contents(
                $path,
                "nis,nama,jenis_kelamin\n" .
                "10001,Budi Santoso,L\n" .
                "10002,Siti Aminah,P\n"
            );

            $service = app(CsvImportService::class);

            $hasil = $service->import(
                $path,
                [
                    'import_class' => TestCsvImport::class,
                ]
            );

            $this->assertIsArray($hasil);

            $this->assertEquals(
                2,
                $hasil['total']
            );

            $this->assertEquals(
                2,
                $hasil['success']
            );

            $this->assertEquals(
                0,
                $hasil['failed']
            );

            $this->assertEmpty(
                $hasil['errors']
            );
        } finally {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Test CSV Importer
|--------------------------------------------------------------------------
*/

class TestCsvImport
{
    public function import(
        string $path,
        array $options = []
    ): array {
        $rows = array_map(
            'str_getcsv',
            file($path)
        );

        // Baris pertama adalah header.
        $dataRows = array_slice(
            $rows,
            1
        );

        $total = count($dataRows);

        return [
            'total' => $total,
            'success' => $total,
            'failed' => 0,
            'errors' => [],
        ];
    }
}