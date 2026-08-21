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
                'siswaTahun',
                'tarifPembayaran',
            ])
            ->latest()
            ->get();
    }

    public function find(int $id): DiskonPembayaran
    {
        return DiskonPembayaran::query()
            ->with([
                'siswaTahun',
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
            'siswaTahun',
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
            'is_active' => !$diskonPembayaran->is_active,
        ]);

        return $diskonPembayaran->fresh([
            'siswaTahun',
            'tarifPembayaran',
        ]);
    }

    public function getBySiswaTahun(int $siswaTahunId): Collection
    {
        return DiskonPembayaran::query()
            ->with('tarifPembayaran')
            ->where('siswa_tahun_id', $siswaTahunId)
            ->latest()
            ->get();
    }

    public function getActiveBySiswaTahun(
        int $siswaTahunId
    ): Collection {
        return DiskonPembayaran::query()
            ->with('tarifPembayaran')
            ->where('siswa_tahun_id', $siswaTahunId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query
                    ->whereNull('tanggal_mulai')
                    ->orWhereDate(
                        'tanggal_mulai',
                        '<=',
                        now()
                    );
            })
            ->where(function ($query) {
                $query
                    ->whereNull('tanggal_selesai')
                    ->orWhereDate(
                        'tanggal_selesai',
                        '>=',
                        now()
                    );
            })
            ->latest()
            ->get();
    }
}