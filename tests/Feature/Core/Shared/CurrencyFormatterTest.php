<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Support\Number\CurrencyFormatter;
use Tests\TestCase;

class CurrencyFormatterTest extends TestCase
{
    public function test_currency_rupiah_dapat_diformat(): void
    {
        $hasil = CurrencyFormatter::rupiah(
            1500000
        );

        $this->assertEquals(
            'Rp 1.500.000',
            $hasil
        );
    }

    public function test_currency_rupiah_tanpa_symbol(): void
    {
        $hasil = CurrencyFormatter::rupiah(
            1500000,
            false
        );

        $this->assertEquals(
            '1.500.000',
            $hasil
        );
    }

    public function test_currency_rupiah_dengan_decimal(): void
    {
        $hasil = CurrencyFormatter::rupiahWithDecimal(
            1500000.50
        );

        $this->assertEquals(
            'Rp 1.500.000,50',
            $hasil
        );
    }

    public function test_currency_null(): void
    {
        $this->assertEquals(
            'Rp 0',
            CurrencyFormatter::rupiah(null)
        );

        $this->assertEquals(
            'Rp 0,00',
            CurrencyFormatter::rupiahWithDecimal(null)
        );
    }

    public function test_currency_raw(): void
    {
        $hasil = CurrencyFormatter::raw(
            '2.500.000,75'
        );

        $this->assertEquals(
            2500000.75,
            $hasil
        );
    }
}