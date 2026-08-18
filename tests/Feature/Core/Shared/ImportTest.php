<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Services\Import\ExcelImportService;
use App\Core\Shared\Support\Excel\ImportHelper;
use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ImportTest extends TestCase
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

    public function test_file_excel_dapat_divalidasi(): void
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

        $this->assertEquals(
            'xlsx',
            ImportHelper::extension($file)
        );
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
    }

    public function test_format_file_yang_tidak_didukung_ditolak(): void
    {
        $file = UploadedFile::fake()->create(
            'siswa.pdf',
            100,
            'application/pdf'
        );

        $this->expectException(
            \InvalidArgumentException::class
        );

        ImportHelper::validateFile($file);
    }

    public function test_nama_file_asli_dapat_diambil(): void
    {
        $file = UploadedFile::fake()->create(
            'data-siswa.xlsx',
            100,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $this->assertEquals(
            'data-siswa',
            ImportHelper::originalName($file)
        );
    }

    public function test_excel_import_service_dapat_dijalankan(): void
    {
        $path = 'test/import/siswa.xlsx';

        Storage::disk('local')->put(
            $path,
            'dummy excel content'
        );

        Excel::fake();

        $service = app(ExcelImportService::class);

        $hasil = $service->import(
            $path,
            [
                'import_class' => TestExcelImport::class,
            ]
        );

        $this->assertIsArray($hasil);

        $this->assertArrayHasKey(
            'total',
            $hasil
        );

        $this->assertArrayHasKey(
            'success',
            $hasil
        );

        $this->assertArrayHasKey(
            'failed',
            $hasil
        );

        $this->assertArrayHasKey(
            'errors',
            $hasil
        );

        $this->assertEquals(
            0,
            $hasil['total']
        );

        $this->assertEquals(
            0,
            $hasil['success']
        );

        $this->assertEquals(
            0,
            $hasil['failed']
        );

        $this->assertEquals(
            [],
            $hasil['errors']
        );
    }

    public function test_excel_sungguhan_dapat_dibaca(): void
    {
        $fileName = 'test-import-siswa.xlsx';

        $data = [
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

        Excel::store(
            new TestExcelImportExport($data),
            $fileName,
            'local'
        );

        $this->assertTrue(
            Storage::disk('local')->exists($fileName)
        );

        $service = app(ExcelImportService::class);

        $hasil = $service->import(
            $fileName,
            [
                'import_class' => RealTestImport::class,
            ]
        );

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
    }
}

/*
|--------------------------------------------------------------------------
| Test Excel Import
|--------------------------------------------------------------------------
*/

class TestExcelImport implements ToArray, WithHeadingRow
{
    public function array(array $rows): void
    {
    }

    public function getTotal(): int
    {
        return 0;
    }

    public function getSuccess(): int
    {
        return 0;
    }

    public function getFailed(): int
    {
        return 0;
    }

    public function getErrors(): array
    {
        return [];
    }
}

/*
|--------------------------------------------------------------------------
| Test Excel Export
|--------------------------------------------------------------------------
*/

class TestExcelImportExport implements FromArray
{
    public function __construct(
        protected array $data
    ) {
    }

    public function array(): array
    {
        return array_merge(
            [
                [
                    'nis',
                    'nama',
                    'jenis_kelamin',
                ],
            ],
            $this->data
        );
    }
}

/*
|--------------------------------------------------------------------------
| Real Test Import
|--------------------------------------------------------------------------
*/

class RealTestImport implements ToArray, WithHeadingRow
{
    protected int $total = 0;

    protected int $success = 0;

    protected int $failed = 0;

    protected array $errors = [];

    public function array(array $rows): void
    {
        foreach ($rows as $index => $row) {
            $this->total++;

            if (
                empty($row['nis']) ||
                empty($row['nama'])
            ) {
                $this->failed++;

                $this->errors[] = [
                    'row' => $index + 2,
                    'message' => 'NIS dan nama wajib diisi.',
                ];

                continue;
            }

            $this->success++;
        }
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function getFailed(): int
    {
        return $this->failed;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}