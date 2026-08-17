<?php

namespace App\Modules\Siswa\Services;

use App\Modules\Siswa\Models\SiswaTahun;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SiswaTahunService
{
    private const STATUS = [
        'AKTIF',
        'LULUS',
        'PINDAH',
        'KELUAR',
    ];

    /**
     * Ambil seluruh riwayat tahun siswa.
     */
    public function all(): Collection
    {
        return SiswaTahun::query()
            ->with([
                'siswa',
                'tahunAjaran',
                'kelompokRombel.rombel',
            ])
            ->orderByDesc('tahun_ajaran_id')
            ->get();
    }

    /**
     * Ambil riwayat pendidikan seorang siswa.
     */
    public function bySiswa(int $siswaId): Collection
    {
        return SiswaTahun::query()
            ->with([
                'tahunAjaran',
                'kelompokRombel.rombel',
            ])
            ->where('siswa_id', $siswaId)
            ->orderByDesc('tahun_ajaran_id')
            ->get();
    }

    /**
     * Ambil data siswa pada tahun ajaran tertentu.
     */
    public function byTahunAjaran(
        int $tahunAjaranId
    ): Collection {
        return SiswaTahun::query()
            ->with([
                'siswa',
                'kelompokRombel.rombel',
            ])
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('kelompok_rombel_id')
            ->get();
    }

    /**
     * Ambil siswa aktif pada tahun ajaran tertentu.
     */
    public function aktifByTahunAjaran(
        int $tahunAjaranId
    ): Collection {
        return SiswaTahun::query()
            ->with([
                'siswa',
                'kelompokRombel.rombel',
            ])
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('status', 'AKTIF')
            ->orderBy('kelompok_rombel_id')
            ->get();
    }

    /**
     * Ambil siswa berdasarkan kelompok rombel.
     */
    public function byKelompokRombel(
        int $kelompokRombelId
    ): Collection {
        return SiswaTahun::query()
            ->with([
                'siswa',
                'tahunAjaran',
                'kelompokRombel.rombel',
            ])
            ->where('kelompok_rombel_id', $kelompokRombelId)
            ->orderBy('siswa_id')
            ->get();
    }

    /**
     * Ambil penempatan siswa pada tahun ajaran tertentu.
     */
    public function findBySiswaAndTahun(
        int $siswaId,
        int $tahunAjaranId
    ): ?SiswaTahun {
        return SiswaTahun::query()
            ->with([
                'siswa',
                'tahunAjaran',
                'kelompokRombel.rombel',
            ])
            ->where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->first();
    }

    /**
     * Buat penempatan siswa.
     *
     * tenant_id diisi otomatis oleh BelongsToTenant.
     */
    public function create(array $data): SiswaTahun
    {
        return DB::transaction(function () use ($data) {
            return SiswaTahun::create($data)->load([
                'siswa',
                'tahunAjaran',
                'kelompokRombel.rombel',
            ]);
        });
    }

    /**
     * Update penempatan siswa.
     */
    public function update(
        SiswaTahun $siswaTahun,
        array $data
    ): SiswaTahun {
        return DB::transaction(function () use (
            $siswaTahun,
            $data
        ) {
            $siswaTahun->update($data);

            return $siswaTahun->refresh()->load([
                'siswa',
                'tahunAjaran',
                'kelompokRombel.rombel',
            ]);
        });
    }

    /**
     * Ubah status siswa.
     */
    public function updateStatus(
        SiswaTahun $siswaTahun,
        string $status
    ): SiswaTahun {
        if (! in_array($status, self::STATUS, true)) {
            throw new InvalidArgumentException(
                'Status siswa tidak valid.'
            );
        }

        $siswaTahun->update([
            'status' => $status,
        ]);

        return $siswaTahun->refresh()->load([
            'siswa',
            'tahunAjaran',
            'kelompokRombel.rombel',
        ]);
    }
}