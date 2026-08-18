<?php

namespace App\Core\Shared\Support\Number;

class CurrencyFormatter
{
    public static function rupiah(
        int|float|null $value,
        bool $symbol = true
    ): string {
        $number = NumberFormatter::format(
            $value,
            0,
            ',',
            '.'
        );

        return $symbol
            ? 'Rp ' . $number
            : $number;
    }

    public static function rupiahWithDecimal(
        int|float|null $value,
        bool $symbol = true
    ): string {
        $number = NumberFormatter::format(
            $value,
            2,
            ',',
            '.'
        );

        return $symbol
            ? 'Rp ' . $number
            : $number;
    }

    public static function raw(
        string|int|float|null $value
    ): float {
        return NumberFormatter::raw($value);
    }
}