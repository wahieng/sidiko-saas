<?php

namespace App\Core\Shared\Support\Excel;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class ImportHelper
{
    /**
     * Ekstensi file Excel yang diperbolehkan.
     */
    protected const ALLOWED_EXTENSIONS = [
        'xlsx',
        'xls',
        'csv',
    ];

    /**
     * Ukuran maksimum file dalam KB.
     */
    protected const MAX_FILE_SIZE_KB = 10240; // 10 MB

    /**
     * Validasi file import.
     */
    public static function validateFile(
        UploadedFile $file
    ): void {
        if (! $file->isValid()) {
            throw new InvalidArgumentException(
                'File import tidak valid.'
            );
        }

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        if (! in_array(
            $extension,
            self::ALLOWED_EXTENSIONS,
            true
        )) {
            throw new InvalidArgumentException(
                'Format file tidak didukung. Gunakan XLSX, XLS, atau CSV.'
            );
        }

        if (
            $file->getSize() >
            self::MAX_FILE_SIZE_KB * 1024
        ) {
            throw new InvalidArgumentException(
                'Ukuran file maksimal 10 MB.'
            );
        }
    }

    /**
     * Ambil ekstensi file.
     */
    public static function extension(
        UploadedFile $file
    ): string {
        return strtolower(
            $file->getClientOriginalExtension()
        );
    }

    /**
     * Cek apakah file merupakan file Excel.
     */
    public static function isExcel(
        UploadedFile $file
    ): bool {
        return in_array(
            self::extension($file),
            ['xlsx', 'xls'],
            true
        );
    }

    /**
     * Cek apakah file CSV.
     */
    public static function isCsv(
        UploadedFile $file
    ): bool {
        return self::extension($file) === 'csv';
    }

    /**
     * Ambil nama file asli tanpa ekstensi.
     */
    public static function originalName(
        UploadedFile $file
    ): string {
        return pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );
    }
}