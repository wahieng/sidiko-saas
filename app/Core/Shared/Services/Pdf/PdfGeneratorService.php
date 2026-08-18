<?php

namespace App\Core\Shared\Services\Pdf;

use App\Core\Shared\Contracts\PdfGeneratorInterface;
use App\Core\Shared\Exceptions\PdfException;
use App\Core\Shared\Support\Pdf\PdfHelper;
use Illuminate\Support\Facades\Storage;

class PdfGeneratorService implements PdfGeneratorInterface
{
    public function generate(
        string $filename,
        mixed $view,
        array $data = [],
        array $options = []
    ): string {
        try {
            PdfHelper::validateFilename($filename);

            $filename = PdfHelper::normalizeFilename($filename);

            $disk = $options['disk'] ?? 'local';

            $directory = trim(
                $options['directory'] ?? 'exports',
                '/'
            );

            $path = $directory . '/' . $filename;

            if (! view()->exists($view)) {
                throw new PdfException(
                    "View PDF [{$view}] tidak ditemukan."
                );
            }

            $pdf = $this->makePdf(
                $view,
                $data,
                $options
            );

            Storage::disk($disk)->put(
                $path,
                $pdf
            );

            if (! Storage::disk($disk)->exists($path)) {
                throw new PdfException(
                    'File PDF gagal disimpan.'
                );
            }

            return $path;
        } catch (PdfException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new PdfException(
                'Gagal membuat PDF: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    protected function makePdf(
        string $view,
        array $data,
        array $options = []
    ): string {
        $engine = $options['engine'] ?? 'dompdf';

        if ($engine !== 'dompdf') {
            throw new PdfException(
                "PDF engine [{$engine}] tidak didukung."
            );
        }

        if (! class_exists(
            \Barryvdh\DomPDF\Facade\Pdf::class
        )) {
            throw new PdfException(
                'PDF engine DomPDF belum terpasang.'
            );
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView(
            $view,
            $data
        )->output();
    }
}