<?php

namespace App\Core\Shared\Support\Number;

class NumberFormatter
{
    public static function format(
        int|float|null $value,
        int $decimals = 0,
        string $decimalSeparator = ',',
        string $thousandsSeparator = '.'
    ): string {
        if ($value === null) {
            return number_format(
                0,
                $decimals,
                $decimalSeparator,
                $thousandsSeparator
            );
        }

        return number_format(
            $value,
            $decimals,
            $decimalSeparator,
            $thousandsSeparator
        );
    }

    public static function integer(
        int|float|null $value
    ): string {
        return self::format($value);
    }

    public static function decimal(
        int|float|null $value,
        int $decimals = 2
    ): string {
        return self::format(
            $value,
            $decimals
        );
    }

    public static function raw(
        string|int|float|null $value
    ): float {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_string($value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }

        return (float) $value;
    }
}