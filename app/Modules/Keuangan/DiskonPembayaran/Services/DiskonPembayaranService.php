<?php

namespace App\Modules\Keuangan\DiskonPembayaran\Services;

use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use Illuminate\Database\Eloquent\Collection;

class DiskonPembayaranService
{
    public function getAll(): Collection
    {
        return DiskonPembayaran::query()
            ->with([
                'siswa',
                'tarifPembayaran',
            ])
            ->latest()
            ->get();
    }

    public function find(int $id): DiskonPembayaran
    {
        return DiskonPembayaran::query()
            ->with([
                'siswa',
                'tarifPembayaran',
            ])
            ->findOrFail($id);
    }

    public function create(array $data): DiskonPembayaran
    {
        return DiskonPembayaran::create($data);
    }

    public function update(
        DiskonPembayaran $diskonPembayaran,
        array $data
    ): DiskonPembayaran {
        $diskonPembayaran->update($data);

        return $diskonPembayaran->fresh([
            'siswa',
            'tarifPembayaran',
        ]);
    }

    public function delete(DiskonPembayaran $diskonPembayaran): bool
    {
        return $diskonPembayaran->delete();
    }

    public function toggleStatus(
        DiskonPembayaran $diskonPembayaran
    ): DiskonPembayaran {
        $diskonPembayaran->update([
            'aktif' => !$diskonPembayaran->aktif,
        ]);

        return $diskonPembayaran->fresh();
    }

    public function getBySiswa(int $siswaId): Collection
    {
        return DiskonPembayaran::query()
            ->with('tarifPembayaran')
            ->where('siswa_id', $siswaId)
            ->latest()
            ->get();
    }

    public function getActiveBySiswa(int $siswaId): Collection
    {
        return DiskonPembayaran::query()
            ->with('tarifPembayaran')
            ->where('siswa_id', $siswaId)
            ->where('aktif', true)
            ->where(function ($query) {
                $query
                    ->whereNull('tanggal_mulai')
                    ->orWhereDate('tanggal_mulai', '<=', now());
            })
            ->where(function ($query) {
                $query
                    ->whereNull('tanggal_selesai')
                    ->orWhereDate('tanggal_selesai', '>=', now());
            })
            ->get();
    }
}