<?php

namespace App\Modules\Akademik\Services;

use App\Modules\Akademik\Models\Rombel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RombelService
{
    /**
     * Ambil semua rombel aktif.
     */
    public function allAktif(): Collection
    {
        return Rombel::query()
            ->where('aktif', true)
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Ambil semua rombel.
     */
    public function all(): Collection
    {
        return Rombel::query()
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Cari rombel berdasarkan kode.
     */
    public function findByKode(string $kode): ?Rombel
    {
        return Rombel::query()
            ->where('kode', $kode)
            ->first();
    }

    /**
     * Buat rombel.
     *
     * tenant_id otomatis diisi oleh BelongsToTenant.
     */
    public function create(array $data): Rombel
    {
        return DB::transaction(function () use ($data) {
            return Rombel::create($data);
        });
    }

    /**
     * Update rombel.
     *
     * Model yang diterima harus berasal dari tenant aktif.
     */
    public function update(
        Rombel $rombel,
        array $data
    ): Rombel {
        return DB::transaction(function () use (
            $rombel,
            $data
        ) {
            $rombel->update($data);

            return $rombel->refresh();
        });
    }

    /**
     * Aktifkan rombel.
     */
    public function aktifkan(Rombel $rombel): Rombel
    {
        return DB::transaction(function () use ($rombel) {
            $rombel->update([
                'aktif' => true,
            ]);

            return $rombel->refresh();
        });
    }

    /**
     * Nonaktifkan rombel.
     *
     * Tidak boleh jika masih memiliki
     * kelompok rombel aktif.
     */
    public function nonaktifkan(Rombel $rombel): Rombel
    {
        return DB::transaction(function () use ($rombel) {

            $masihDigunakan = $rombel
                ->kelompokRombel()
                ->where('aktif', true)
                ->exists();

            if ($masihDigunakan) {
                throw new RuntimeException(
                    'Rombel masih memiliki kelompok rombel aktif.'
                );
            }

            $rombel->update([
                'aktif' => false,
            ]);

            return $rombel->refresh();
        });
    }
}