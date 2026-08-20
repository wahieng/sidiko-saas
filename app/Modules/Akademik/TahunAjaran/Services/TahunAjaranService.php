<?php

namespace App\Modules\Akademik\TahunAjaran\Services;

use App\Core\Tenant\Context\TenantContext;
use App\Modules\Akademik\TahunAjaran\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TahunAjaranService
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    /**
     * Ambil semua tahun ajaran tenant aktif.
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
     * Buat tahun ajaran untuk tenant aktif.
     */
    public function create(array $data): TahunAjaran
    {
        return DB::transaction(function () use ($data) {
            $tenant = $this->tenantContext->require();

            $data['tenant_id'] = $tenant->id;

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
     */
    public function update(
        TahunAjaran $tahunAjaran,
        array $data
    ): TahunAjaran {
        return DB::transaction(function () use (
            $tahunAjaran,
            $data
        ) {
            /*
             * Pastikan model berasal dari tenant aktif.
             */
            $tenant = $this->tenantContext->require();

            if ((int) $tahunAjaran->tenant_id !== (int) $tenant->id) {
                throw new RuntimeException(
                    'Tahun ajaran bukan milik tenant aktif.'
                );
            }

            /*
             * Tenant ID tidak boleh diubah melalui update.
             */
            unset($data['tenant_id']);

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
            $tenant = $this->tenantContext->require();

            if ((int) $tahunAjaran->tenant_id !== (int) $tenant->id) {
                throw new RuntimeException(
                    'Tahun ajaran bukan milik tenant aktif.'
                );
            }

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
     * Tidak boleh menonaktifkan satu-satunya
     * tahun ajaran aktif pada tenant.
     */
    public function nonaktifkan(
        TahunAjaran $tahunAjaran
    ): TahunAjaran {
        $tenant = $this->tenantContext->require();

        if ((int) $tahunAjaran->tenant_id !== (int) $tenant->id) {
            throw new RuntimeException(
                'Tahun ajaran bukan milik tenant aktif.'
            );
        }

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