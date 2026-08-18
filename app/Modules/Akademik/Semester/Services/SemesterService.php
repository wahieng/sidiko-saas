<?php

namespace App\Modules\Akademik\Semester\Services;

use App\Modules\Akademik\Semester\Models\Semester;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SemesterService
{
    /**
     * Ambil semua semester.
     */
    public function all(): Collection
    {
        return Semester::query()
            ->with('tahunAjaran')
            ->orderBy('tanggal_mulai')
            ->get();
    }

    /**
     * Ambil semester berdasarkan tahun ajaran.
     */
    public function byTahunAjaran(
        int $tahunAjaranId
    ): Collection {
        return Semester::query()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('tanggal_mulai')
            ->get();
    }

    /**
     * Ambil semester aktif.
     */
    public function aktif(): ?Semester
    {
        return Semester::query()
            ->where('aktif', true)
            ->orderByDesc('tanggal_mulai')
            ->first();
    }

    /**
     * Ambil semester aktif berdasarkan tahun ajaran.
     */
    public function aktifByTahunAjaran(
        int $tahunAjaranId
    ): ?Semester {
        return Semester::query()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('aktif', true)
            ->first();
    }

    /**
     * Cari semester berdasarkan tahun ajaran dan kode.
     */
    public function findByKode(
        int $tahunAjaranId,
        string $kode
    ): ?Semester {
        return Semester::query()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('kode', $kode)
            ->first();
    }

    /**
     * Buat semester.
     */
    public function create(array $data): Semester
    {
        return DB::transaction(function () use ($data) {

            if (($data['aktif'] ?? false) === true) {
                $this->nonaktifkanSemesterLain(
                    $data['tahun_ajaran_id']
                );
            }

            return Semester::create($data);
        });
    }

    /**
     * Update semester.
     */
    public function update(
        Semester $semester,
        array $data
    ): Semester {
        return DB::transaction(function () use (
            $semester,
            $data
        ) {
            if (($data['aktif'] ?? false) === true) {
                $this->nonaktifkanSemesterLain(
                    $semester->tahun_ajaran_id,
                    $semester->id
                );
            }

            $semester->update($data);

            return $semester->refresh();
        });
    }

    /**
     * Aktifkan semester.
     */
    public function aktifkan(
        Semester $semester
    ): Semester {
        return DB::transaction(function () use ($semester) {

            $this->nonaktifkanSemesterLain(
                $semester->tahun_ajaran_id,
                $semester->id
            );

            $semester->update([
                'aktif' => true,
            ]);

            return $semester->refresh();
        });
    }

    /**
     * Nonaktifkan semester.
     *
     * Tidak boleh jika semester ini
     * merupakan satu-satunya semester aktif
     * dalam tahun ajaran.
     */
    public function nonaktifkan(
        Semester $semester
    ): Semester {
        if (! $semester->aktif) {
            return $semester;
        }

        $semesterAktifLain = Semester::query()
            ->where('tahun_ajaran_id', $semester->tahun_ajaran_id)
            ->where('id', '!=', $semester->id)
            ->where('aktif', true)
            ->exists();

        if (! $semesterAktifLain) {
            throw new RuntimeException(
                'Tidak dapat menonaktifkan satu-satunya semester aktif dalam tahun ajaran.'
            );
        }

        $semester->update([
            'aktif' => false,
        ]);

        return $semester->refresh();
    }

    /**
     * Nonaktifkan semester aktif lain
     * dalam tahun ajaran yang sama.
     */
    protected function nonaktifkanSemesterLain(
        int $tahunAjaranId,
        ?int $excludeId = null
    ): void {
        $query = Semester::query()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('aktif', true);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update([
            'aktif' => false,
        ]);
    }
}