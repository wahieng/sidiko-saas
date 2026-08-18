<?php

namespace App\Core\Shared\Services\Import;

use App\Core\Shared\Contracts\ImporterInterface;
use App\Core\Shared\Exceptions\ImportException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ExcelImportService implements ImporterInterface
{
    public function import(
        string $path,
        array $options = []
    ): array {
        try {
            if (! Storage::disk('local')->exists($path)) {
                throw new ImportException(
                    "File import tidak ditemukan: {$path}"
                );
            }

            $importClass = $options['import_class'] ?? null;

            if (! $importClass) {
                throw new ImportException(
                    'Import class belum ditentukan.'
                );
            }

            $fullPath = Storage::disk('local')->path($path);

            $importer = app($importClass);

            Excel::import(
                $importer,
                $fullPath
            );

            return [
                'total' => method_exists($importer, 'getTotal')
                    ? $importer->getTotal()
                    : 0,

                'success' => method_exists($importer, 'getSuccess')
                    ? $importer->getSuccess()
                    : 0,

                'failed' => method_exists($importer, 'getFailed')
                    ? $importer->getFailed()
                    : 0,

                'errors' => method_exists($importer, 'getErrors')
                    ? $importer->getErrors()
                    : [],
            ];
        } catch (ImportException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ImportException(
                'Proses import Excel gagal.',
                0,
                $e
            );
        }
    }

    /**
     * Import langsung dari UploadedFile.
     */
    public function importUploadedFile(
        UploadedFile $file,
        string $importClass
    ): array {
        try {
            if (! $file->isValid()) {
                throw new ImportException(
                    'File upload tidak valid.'
                );
            }

            $importer = app($importClass);

            Excel::import(
                $importer,
                $file
            );

            return [
                'total' => method_exists($importer, 'getTotal')
                    ? $importer->getTotal()
                    : 0,

                'success' => method_exists($importer, 'getSuccess')
                    ? $importer->getSuccess()
                    : 0,

                'failed' => method_exists($importer, 'getFailed')
                    ? $importer->getFailed()
                    : 0,

                'errors' => method_exists($importer, 'getErrors')
                    ? $importer->getErrors()
                    : [],
            ];
        } catch (ImportException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ImportException(
                'Proses upload dan import Excel gagal.',
                0,
                $e
            );
        }
    }
}