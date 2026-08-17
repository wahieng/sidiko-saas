<?php

namespace App\Modules\Siswa\Services;

use App\Modules\Siswa\Models\Siswa;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SiswaService
{
    /**
     * Ambil seluruh siswa.
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
        return Siswa::find($id);
    }

    /**
     * Cari siswa berdasarkan NIS.
     */
    public function findByNis(string $nis): ?Siswa
    {
        return Siswa::where('nis', $nis)->first();
    }

    /**
     * Cari siswa berdasarkan NISN.
     */
    public function findByNisn(string $nisn): ?Siswa
    {
        return Siswa::where('nisn', $nisn)->first();
    }

    /**
     * Cari siswa berdasarkan NIK.
     */
    public function findByNik(string $nik): ?Siswa
    {
        return Siswa::where('nik', $nik)->first();
    }

    /**
     * Buat siswa baru.
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
    public function update(Siswa $siswa, array $data): Siswa
    {
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