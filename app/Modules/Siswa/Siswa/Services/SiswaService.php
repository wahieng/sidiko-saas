<?php

namespace App\Modules\Siswa\Siswa\Services;

use App\Modules\Siswa\Siswa\Models\Siswa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SiswaService
{
    /**
     * Ambil seluruh siswa dalam tenant aktif.
     */
    public function all(): Collection
    {
        return Siswa::query()
            ->orderBy('nama')
            ->get();
    }

    /**
     * Ambil siswa dengan pagination.
     */
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return Siswa::query()
            ->orderBy('nama')
            ->paginate($perPage);
    }

    /**
     * Cari siswa berdasarkan ID.
     */
    public function find(int $id): ?Siswa
    {
        return Siswa::query()
            ->find($id);
    }

    /**
     * Cari siswa berdasarkan NIS.
     */
    public function findByNis(string $nis): ?Siswa
    {
        return Siswa::query()
            ->where('nis', $nis)
            ->first();
    }

    /**
     * Cari siswa berdasarkan NISN.
     */
    public function findByNisn(string $nisn): ?Siswa
    {
        return Siswa::query()
            ->where('nisn', $nisn)
            ->first();
    }

    /**
     * Cari siswa berdasarkan NIK.
     */
    public function findByNik(string $nik): ?Siswa
    {
        return Siswa::query()
            ->where('nik', $nik)
            ->first();
    }

    /**
     * Ambil siswa beserta riwayat tahun ajaran.
     */
    public function withRiwayat(int $id): ?Siswa
    {
        return Siswa::query()
            ->with([
                'siswaTahun.tahunAjaran',
                'siswaTahun.kelompokRombel.rombel',
            ])
            ->find($id);
    }

    /**
     * Ambil siswa beserta data tahun ajaran aktif.
     */
    public function withTahunAjaranAktif(int $id): ?Siswa
    {
        return Siswa::query()
            ->with([
                'siswaTahun' => function ($query) {
                    $query->where('status', 'AKTIF')
                        ->with([
                            'tahunAjaran',
                            'kelompokRombel.rombel',
                        ]);
                },
            ])
            ->find($id);
    }

    /**
     * Buat siswa baru.
     *
     * tenant_id diisi otomatis oleh BelongsToTenant.
     */
    public function create(array $data): Siswa
    {
        return DB::transaction(function () use ($data) {
            return Siswa::create($data);
        });
    }

    /**
     * Update identitas siswa.
     */
    public function update(
        Siswa $siswa,
        array $data
    ): Siswa {
        return DB::transaction(function () use ($siswa, $data) {
            $siswa->update($data);

            return $siswa->refresh();
        });
    }

    /**
     * Hapus siswa.
     */
    public function delete(Siswa $siswa): bool
    {
        return DB::transaction(function () use ($siswa) {
            return (bool) $siswa->delete();
        });
    }
}