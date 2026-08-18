<?php

namespace App\Modules\Siswa\Services;

use App\Modules\Siswa\Models\Wali;
use Illuminate\Database\Eloquent\Collection;

class WaliService
{
    /**
     * Ambil semua wali milik siswa.
     */
    public function bySiswa(int $siswaId): Collection
    {
        return Wali::query()
            ->where('siswa_id', $siswaId)
            ->orderBy('nama')
            ->get();
    }

    /**
     * Ambil wali berdasarkan ID.
     */
    public function find(int $id): ?Wali
    {
        return Wali::query()
            ->find($id);
    }

    /**
     * Ambil wali berdasarkan ID siswa.
     *
     * Karena wali bersifat opsional,
     * hasil bisa null.
     */
    public function firstBySiswa(int $siswaId): ?Wali
    {
        return Wali::query()
            ->where('siswa_id', $siswaId)
            ->first();
    }

    /**
     * Simpan data wali.
     */
    public function create(array $data): Wali
    {
        return Wali::query()->create($data);
    }

    /**
     * Update data wali.
     */
    public function update(Wali $wali, array $data): Wali
    {
        $wali->update($data);

        return $wali->refresh();
    }

    /**
     * Hapus data wali.
     */
    public function delete(Wali $wali): bool
    {
        return (bool) $wali->delete();
    }
}