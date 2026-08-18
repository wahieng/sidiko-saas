<?php

namespace App\Core\Shared\Services\Export;

use App\Core\Shared\Contracts\ExporterInterface;
use App\Core\Shared\Exceptions\ExportException;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportService implements ExporterInterface
{
    public function export(
        string $filename,
        array $data,
        array $options = []
    ): string {
        try {
            if ($filename === '') {
                throw new ExportException(
                    'Nama file export tidak boleh kosong.'
                );
            }

            $directory = $options['directory'] ?? 'exports';

            $disk = $options['disk'] ?? 'local';

            $path = trim(
                $directory . '/' . $filename,
                '/'
            );

            $exportClass = $options['export_class'] ?? null;

            if (! $exportClass) {
                throw new ExportException(
                    'Export class belum ditentukan.'
                );
            }

            if (! class_exists($exportClass)) {
                throw new ExportException(
                    "Export class [{$exportClass}] tidak ditemukan."
                );
            }

            Excel::store(
                app($exportClass, [
                    'data' => $data,
                ]),
                $path,
                $disk
            );

            if (! Storage::disk($disk)->exists($path)) {
                throw new ExportException(
                    'File Excel gagal dibuat.'
                );
            }

            return $path;
        } catch (ExportException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ExportException(
                'Gagal melakukan export Excel: ' . $e->getMessage(),
                previous: $e
            );
        }
    }
}