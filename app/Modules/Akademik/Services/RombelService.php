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
     */
    public function create(array $data): Rombel
    {
        return DB::transaction(function () use ($data) {
            return Rombel::create($data);
        });
    }

    /**
     * Update rombel.
     */
    public function update(
        Rombel $rombel,
        array $data
    ): Rombel {
        $rombel->update($data);

        return $rombel->refresh();
    }

    /**
     * Aktifkan rombel.
     */
    public function aktifkan(Rombel $rombel): Rombel
    {
        $rombel->update([
            'aktif' => true,
        ]);

        return $rombel->refresh();
    }

    /**
     * Nonaktifkan rombel.
     */
    public function nonaktifkan(Rombel $rombel): Rombel
    {
        if (
            $rombel->kelompokRombel()
                ->where('aktif', true)
                ->exists()
        ) {
            throw new RuntimeException(
                'Rombel masih memiliki kelompok rombel aktif.'
            );
        }

        $rombel->update([
            'aktif' => false,
        ]);

        return $rombel->refresh();
    }
}