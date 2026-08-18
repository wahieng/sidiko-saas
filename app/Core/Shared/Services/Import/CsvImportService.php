<?php

namespace App\Core\Shared\Services\Import;

use App\Core\Shared\Contracts\ImporterInterface;
use App\Core\Shared\Exceptions\ImportException;

class CsvImportService implements ImporterInterface
{
    public function import(
        string $path,
        array $options = []
    ): array {
        try {
            if (! file_exists($path)) {
                throw new ImportException(
                    'File CSV tidak ditemukan.'
                );
            }

            $importClass = $options['import_class'] ?? null;

            if (! $importClass) {
                throw new ImportException(
                    'Import class belum ditentukan.'
                );
            }

            $importer = app($importClass);

            if (! method_exists($importer, 'import')) {
                throw new ImportException(
                    'Importer harus memiliki method import().'
                );
            }

            $result = $importer->import(
                $path,
                $options
            );

            return [
                'total' => $result['total'] ?? 0,
                'success' => $result['success'] ?? 0,
                'failed' => $result['failed'] ?? 0,
                'errors' => $result['errors'] ?? [],
            ];
        } catch (ImportException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ImportException(
                'Gagal melakukan import CSV: ' . $e->getMessage(),
                previous: $e
            );
        }
    }
}