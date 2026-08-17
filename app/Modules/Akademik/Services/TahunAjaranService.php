<?php

namespace App\Modules\Akademik\Services;

use App\Modules\Akademik\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TahunAjaranService
{
    /**
     * Ambil semua tahun ajaran tenant aktif.
     *
     * Global scope BelongsToTenant akan otomatis
     * membatasi query berdasarkan tenant aktif.
     */
    public function all(): Collection
    {
        return TahunAjaran::query()
            ->orderByDesc('tanggal_mulai')
            ->get();
    }

    /**
     * Ambil tahun ajaran aktif tenant aktif.
     */
    public function aktif(): ?TahunAjaran
    {
        return TahunAjaran::query()
            ->where('aktif', true)
            ->first();
    }

    /**
     * Buat tahun ajaran.
     *
     * tenant_id otomatis diisi oleh BelongsToTenant
     * ketika model dibuat.
     */
    public function create(array $data): TahunAjaran
    {
        return DB::transaction(function () use ($data) {

            if (($data['aktif'] ?? false) === true) {
                TahunAjaran::query()
                    ->where('aktif', true)
                    ->update([
                        'aktif' => false,
                    ]);
            }

            return TahunAjaran::create($data);
        });
    }

    /**
     * Update tahun ajaran.
     *
     * Global scope memastikan model hanya berasal
     * dari tenant aktif.
     */
    public function update(
        TahunAjaran $tahunAjaran,
        array $data
    ): TahunAjaran {
        return DB::transaction(function () use (
            $tahunAjaran,
            $data
        ) {

            if (($data['aktif'] ?? false) === true) {
                TahunAjaran::query()
                    ->where('id', '!=', $tahunAjaran->id)
                    ->where('aktif', true)
                    ->update([
                        'aktif' => false,
                    ]);
            }

            $tahunAjaran->update($data);

            return $tahunAjaran->refresh();
        });
    }

    /**
     * Aktifkan tahun ajaran.
     */
    public function aktifkan(
        TahunAjaran $tahunAjaran
    ): TahunAjaran {
        return DB::transaction(function () use (
            $tahunAjaran
        ) {

            TahunAjaran::query()
                ->where('id', '!=', $tahunAjaran->id)
                ->where('aktif', true)
                ->update([
                    'aktif' => false,
                ]);

            $tahunAjaran->update([
                'aktif' => true,
            ]);

            return $tahunAjaran->refresh();
        });
    }

    /**
     * Nonaktifkan tahun ajaran.
     *
     * Tidak boleh jika tahun ajaran tersebut
     * adalah satu-satunya tahun ajaran aktif
     * pada tenant.
     */
    public function nonaktifkan(
        TahunAjaran $tahunAjaran
    ): TahunAjaran {
        if (! $tahunAjaran->aktif) {
            return $tahunAjaran;
        }

        $tahunAktifLain = TahunAjaran::query()
            ->where('id', '!=', $tahunAjaran->id)
            ->where('aktif', true)
            ->exists();

        if (! $tahunAktifLain) {
            throw new RuntimeException(
                'Tidak dapat menonaktifkan satu-satunya tahun ajaran aktif.'
            );
        }

        $tahunAjaran->update([
            'aktif' => false,
        ]);

        return $tahunAjaran->refresh();
    }
}