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
            ->where('kelompok_rombel.tahun_ajaran_id', $tahunAjaranId)
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
            ->where('kelompok_rombel.tahun_ajaran_id', $tahunAjaranId)
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
     * Ambil kelompok berdasarkan Rombel
     * pada tahun ajaran tertentu.
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
     *
     * tenant_id otomatis diisi oleh BelongsToTenant.
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
        return DB::transaction(function () use (
            $kelompokRombel,
            $data
        ) {
            $kelompokRombel->update($data);

            return $kelompokRombel->refresh();
        });
    }

    /**
     * Aktifkan kelompok rombel.
     */
    public function aktifkan(
        KelompokRombel $kelompokRombel
    ): KelompokRombel {
        return DB::transaction(function () use (
            $kelompokRombel
        ) {
            $kelompokRombel->update([
                'aktif' => true,
            ]);

            return $kelompokRombel->refresh();
        });
    }

    /**
     * Nonaktifkan kelompok rombel.
     */
    public function nonaktifkan(
        KelompokRombel $kelompokRombel
    ): KelompokRombel {
        return DB::transaction(function () use (
            $kelompokRombel
        ) {
            $kelompokRombel->update([
                'aktif' => false,
            ]);

            return $kelompokRombel->refresh();
        });
    }
}