<?php

namespace App\Modules\Akademik\Services;

use App\Modules\Akademik\Models\KelompokRombel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class KelompokRombelService
{
    /**
     * Ambil kelompok rombel berdasarkan tahun ajaran.
     *
     * Urutan:
     * VII-A
     * VII-B
     * VIII-A
     * VIII-B
     * IX-A
     * IX-B
     */
    public function byTahunAjaran(
        int $tahunAjaranId
    ): Collection {
        return KelompokRombel::query()
            ->with('rombel')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->join(
                'rombel',
                'rombel.id',
                '=',
                'kelompok_rombel.rombel_id'
            )
            ->orderBy('rombel.urutan')
            ->orderBy('kelompok_rombel.urutan')
            ->select('kelompok_rombel.*')
            ->get();
    }

    /**
     * Ambil kelompok rombel aktif berdasarkan tahun ajaran.
     */
    public function aktifByTahunAjaran(
        int $tahunAjaranId
    ): Collection {
        return KelompokRombel::query()
            ->with('rombel')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('kelompok_rombel.aktif', true)
            ->join(
                'rombel',
                'rombel.id',
                '=',
                'kelompok_rombel.rombel_id'
            )
            ->orderBy('rombel.urutan')
            ->orderBy('kelompok_rombel.urutan')
            ->select('kelompok_rombel.*')
            ->get();
    }

    /**
     * Ambil semua kelompok berdasarkan Rombel.
     *
     * Contoh:
     * VII → VII-A, VII-B
     */
    public function byRombel(
        int $tahunAjaranId,
        int $rombelId
    ): Collection {
        return KelompokRombel::query()
            ->with('rombel')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('rombel_id', $rombelId)
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Buat kelompok rombel.
     */
    public function create(array $data): KelompokRombel
    {
        return DB::transaction(function () use ($data) {
            return KelompokRombel::create($data);
        });
    }

    /**
     * Update kelompok rombel.
     */
    public function update(
        KelompokRombel $kelompokRombel,
        array $data
    ): KelompokRombel {
        $kelompokRombel->update($data);

        return $kelompokRombel->refresh();
    }

    /**
     * Aktifkan kelompok rombel.
     */
    public function aktifkan(
        KelompokRombel $kelompokRombel
    ): KelompokRombel {
        $kelompokRombel->update([
            'aktif' => true,
        ]);

        return $kelompokRombel->refresh();
    }

    /**
     * Nonaktifkan kelompok rombel.
     */
    public function nonaktifkan(
        KelompokRombel $kelompokRombel
    ): KelompokRombel {
        $kelompokRombel->update([
            'aktif' => false,
        ]);

        return $kelompokRombel->refresh();
    }
}