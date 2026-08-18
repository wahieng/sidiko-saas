<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Support\Number\NumberFormatter;
use Tests\TestCase;

class NumberFormatterTest extends TestCase
{
    public function test_integer_dapat_diformat(): void
    {
        $hasil = NumberFormatter::integer(1500000);

        $this->assertEquals(
            '1.500.000',
            $hasil
        );
    }

    public function test_decimal_dapat_diformat(): void
    {
        $hasil = NumberFormatter::decimal(
            1500000.50
        );

        $this->assertEquals(
            '1.500.000,50',
            $hasil
        );
    }

    public function test_number_formatter_mendukung_null(): void
    {
        $this->assertEquals(
            '0',
            NumberFormatter::integer(null)
        );

        $this->assertEquals(
            '0,00',
            NumberFormatter::decimal(null)
        );
    }

    public function test_string_angka_dapat_dikonversi_ke_numeric(): void
    {
        $hasil = NumberFormatter::raw(
            '1.500.000,50'
        );

        $this->assertEquals(
            1500000.50,
            $hasil
        );
    }

    public function test_raw_null_menghasilkan_nol(): void
    {
        $this->assertEquals(
            0,
            NumberFormatter::raw(null)
        );
    }
}