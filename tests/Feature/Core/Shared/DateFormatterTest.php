<?php

namespace Tests\Feature\Core\Shared;

use App\Core\Shared\Support\Date\DateFormatter;
use Carbon\Carbon;
use Tests\TestCase;

class DateFormatterTest extends TestCase
{
    public function test_tanggal_dapat_diformat(): void
    {
        $hasil = DateFormatter::tanggal('2026-08-18');

        $this->assertEquals(
            '18-08-2026',
            $hasil
        );
    }

    public function test_tanggal_indonesia_dapat_diformat(): void
    {
        $hasil = DateFormatter::tanggalIndonesia(
            '2026-08-18'
        );

        $this->assertEquals(
            '18 Agustus 2026',
            $hasil
        );
    }

    public function test_tanggal_waktu_dapat_diformat(): void
    {
        $hasil = DateFormatter::tanggalWaktu(
            '2026-08-18 14:30:00'
        );

        $this->assertEquals(
            '18-08-2026 14:30',
            $hasil
        );
    }

    public function test_waktu_dapat_diformat(): void
    {
        $hasil = DateFormatter::waktu(
            '2026-08-18 14:30:00'
        );

        $this->assertEquals(
            '14:30',
            $hasil
        );
    }

    public function test_bulan_dapat_diformat(): void
    {
        $hasil = DateFormatter::bulan(
            '2026-08-18'
        );

        $this->assertEquals(
            'Agustus 2026',
            $hasil
        );
    }

    public function test_hari_dapat_diformat(): void
    {
        $hasil = DateFormatter::hari(
            '2026-08-18'
        );

        $this->assertEquals(
            'Selasa',
            $hasil
        );
    }

    public function test_carbon_dapat_diformat(): void
    {
        $date = Carbon::create(
            2026,
            8,
            18,
            14,
            30
        );

        $this->assertEquals(
            '18-08-2026',
            DateFormatter::tanggal($date)
        );

        $this->assertEquals(
            '18-08-2026 14:30',
            DateFormatter::tanggalWaktu($date)
        );
    }

    public function test_null_menghasilkan_strip(): void
    {
        $this->assertEquals(
            '-',
            DateFormatter::tanggal(null)
        );

        $this->assertEquals(
            '-',
            DateFormatter::tanggalIndonesia(null)
        );

        $this->assertEquals(
            '-',
            DateFormatter::tanggalWaktu(null)
        );

        $this->assertEquals(
            '-',
            DateFormatter::waktu(null)
        );

        $this->assertEquals(
            '-',
            DateFormatter::bulan(null)
        );

        $this->assertEquals(
            '-',
            DateFormatter::hari(null)
        );
    }
}