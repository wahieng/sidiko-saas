<?php

namespace App\Modules\Keuangan\TarifPembayaran\Services;

use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TarifPembayaranService
{
    /**
     * Mengambil seluruh tarif pembayaran.
     */
    public function getAll(): Collection
    {
        return TarifPembayaran::query()
            ->with([
                'jenisPembayaran',
                'kelompokRombel.rombel',
                'kelompokRombel.tahunAjaran',
            ])
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Mengambil tarif berdasarkan ID.
     */
    public function find(int $id): TarifPembayaran
    {
        return TarifPembayaran::query()
            ->with([
                'jenisPembayaran',
                'kelompokRombel.rombel',
                'kelompokRombel.tahunAjaran',
            ])
            ->findOrFail($id);
    }

    /**
     * Membuat tarif pembayaran.
     */
    public function create(array $data): TarifPembayaran
    {
        return DB::transaction(function () use ($data) {
            return TarifPembayaran::create($data);
        });
    }

    /**
     * Mengubah tarif pembayaran.
     */
    public function update(
        int $id,
        array $data
    ): TarifPembayaran {
        return DB::transaction(function () use ($id, $data) {
            $tarif = TarifPembayaran::findOrFail($id);

            $tarif->update($data);

            return $tarif->fresh([
                'jenisPembayaran',
                'kelompokRombel.rombel',
                'kelompokRombel.tahunAjaran',
            ]);
        });
    }

    /**
     * Menghapus tarif pembayaran.
     */
    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $tarif = TarifPembayaran::findOrFail($id);

            $tarif->delete();
        });
    }
}