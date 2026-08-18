<?php

namespace App\Core\Shared\Support\Pdf;

class PdfHelper
{
    public static function extension(
        string $filename
    ): string {
        return strtolower(
            pathinfo($filename, PATHINFO_EXTENSION)
        );
    }

    public static function isPdf(
        string $filename
    ): bool {
        return self::extension($filename) === 'pdf';
    }

    public static function normalizeFilename(
        string $filename
    ): string {
        $filename = trim($filename);

        if ($filename === '') {
            return 'document.pdf';
        }

        if (! self::isPdf($filename)) {
            $filename .= '.pdf';
        }

        return $filename;
    }

    public static function validateFilename(
        string $filename
    ): void {
        if (trim($filename) === '') {
            throw new \InvalidArgumentException(
                'Nama file PDF tidak boleh kosong.'
            );
        }
    }
}