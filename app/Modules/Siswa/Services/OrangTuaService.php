<?php

namespace App\Modules\Siswa\Services;

use App\Modules\Siswa\Models\OrangTua;
use Illuminate\Database\Eloquent\Collection;

class OrangTuaService
{
    /**
     * Ambil semua orang tua.
     */
    public function all(): Collection
    {
        return OrangTua::query()
            ->with('siswa')
            ->orderBy('siswa_id')
            ->orderBy('hubungan')
            ->get();
    }

    /**
     * Ambil orang tua berdasarkan ID.
     */
    public function find(int $id): OrangTua
    {
        return OrangTua::query()
            ->with('siswa')
            ->findOrFail($id);
    }

    /**
     * Ambil orang tua berdasarkan siswa.
     */
    public function bySiswa(int $siswaId): Collection
    {
        return OrangTua::query()
            ->where('siswa_id', $siswaId)
            ->orderBy('hubungan')
            ->get();
    }

    /**
     * Ambil ayah seorang siswa.
     */
    public function ayah(int $siswaId): ?OrangTua
    {
        return OrangTua::query()
            ->where('siswa_id', $siswaId)
            ->where('hubungan', 'AYAH')
            ->first();
    }

    /**
     * Ambil ibu seorang siswa.
     */
    public function ibu(int $siswaId): ?OrangTua
    {
        return OrangTua::query()
            ->where('siswa_id', $siswaId)
            ->where('hubungan', 'IBU')
            ->first();
    }

    /**
     * Simpan orang tua.
     */
    public function create(array $data): OrangTua
    {
        return OrangTua::create($data);
    }

    /**
     * Update orang tua.
     */
    public function update(
        OrangTua $orangTua,
        array $data
    ): OrangTua {
        $orangTua->update($data);

        return $orangTua->refresh();
    }

    /**
     * Hapus orang tua.
     */
    public function delete(OrangTua $orangTua): void
    {
        $orangTua->delete();
    }
}