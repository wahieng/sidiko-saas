<?php

namespace App\Core\Shared\Support\Excel;

class ExportHelper
{
    public static function extension(
        string $filename
    ): string {
        return strtolower(
            pathinfo($filename, PATHINFO_EXTENSION)
        );
    }

    public static function isExcel(
        string $filename
    ): bool {
        return in_array(
            self::extension($filename),
            ['xlsx', 'xls'],
            true
        );
    }

    public static function isCsv(
        string $filename
    ): bool {
        return self::extension($filename) === 'csv';
    }

    public static function validateExcel(
        string $filename
    ): void {
        if (! self::isExcel($filename)) {
            throw new \InvalidArgumentException(
                'File harus berformat Excel.'
            );
        }
    }

    public static function validateCsv(
        string $filename
    ): void {
        if (! self::isCsv($filename)) {
            throw new \InvalidArgumentException(
                'File harus berformat CSV.'
            );
        }
    }
}