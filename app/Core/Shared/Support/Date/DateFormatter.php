<?php

namespace App\Core\Shared\Support\Date;

use Carbon\Carbon;
use DateTimeInterface;

class DateFormatter
{
    /**
     * Format tanggal: 18-08-2026
     */
    public static function tanggal(
        DateTimeInterface|string|null $date
    ): string {
        if (empty($date)) {
            return '-';
        }

        return self::parse($date)->format('d-m-Y');
    }

    /**
     * Format tanggal Indonesia: 18 Agustus 2026
     */
    public static function tanggalIndonesia(
        DateTimeInterface|string|null $date
    ): string {
        if (empty($date)) {
            return '-';
        }

        $carbon = self::parse($date);

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $carbon->day
            . ' '
            . $bulan[$carbon->month]
            . ' '
            . $carbon->year;
    }

    /**
     * Format tanggal dan waktu: 18-08-2026 14:30
     */
    public static function tanggalWaktu(
        DateTimeInterface|string|null $date
    ): string {
        if (empty($date)) {
            return '-';
        }

        return self::parse($date)->format('d-m-Y H:i');
    }

    /**
     * Format waktu: 14:30
     */
    public static function waktu(
        DateTimeInterface|string|null $date
    ): string {
        if (empty($date)) {
            return '-';
        }

        return self::parse($date)->format('H:i');
    }

    /**
     * Format bulan dan tahun: Agustus 2026
     */
    public static function bulan(
        DateTimeInterface|string|null $date
    ): string {
        if (empty($date)) {
            return '-';
        }

        $carbon = self::parse($date);

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $bulan[$carbon->month]
            . ' '
            . $carbon->year;
    }

    /**
     * Format nama hari Indonesia.
     */
    public static function hari(
        DateTimeInterface|string|null $date
    ): string {
        if (empty($date)) {
            return '-';
        }

        $carbon = self::parse($date);

        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return $hari[$carbon->dayOfWeek];
    }

    /**
     * Parse berbagai jenis input tanggal.
     */
    protected static function parse(
        DateTimeInterface|string $date
    ): Carbon {
        if ($date instanceof Carbon) {
            return $date;
        }

        if ($date instanceof DateTimeInterface) {
            return Carbon::instance($date);
        }

        return Carbon::parse($date);
    }
}