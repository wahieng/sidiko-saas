<?php

namespace App\Modules\Keuangan\JenisPembayaran\Services;

use App\Core\Tenant\Context\TenantContext;
use App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JenisPembayaranService
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    /**
     * Mengambil semua jenis pembayaran tenant aktif.
     */
    public function all(): Collection
    {
        $tenant = $this->tenantContext->get();

        if (!$tenant) {
            throw new ModelNotFoundException(
                'Tenant aktif tidak ditemukan.'
            );
        }

        return JenisPembayaran::query()
            ->where('tenant_id', $tenant->id)
            ->orderBy('nama')
            ->get();
    }

    /**
     * Mengambil jenis pembayaran berdasarkan ID.
     */
    public function find(int $id): JenisPembayaran
    {
        $tenant = $this->tenantContext->get();

        if (!$tenant) {
            throw new ModelNotFoundException(
                'Tenant aktif tidak ditemukan.'
            );
        }

        return JenisPembayaran::query()
            ->where('tenant_id', $tenant->id)
            ->findOrFail($id);
    }

    /**
     * Membuat jenis pembayaran baru.
     */
    public function create(array $data): JenisPembayaran
    {
        $tenant = $this->tenantContext->get();

        if (!$tenant) {
            throw new ModelNotFoundException(
                'Tenant aktif tidak ditemukan.'
            );
        }

        $data['tenant_id'] = $tenant->id;

        return JenisPembayaran::query()->create($data);
    }

    /**
     * Memperbarui jenis pembayaran.
     */
    public function update(
        int $id,
        array $data
    ): JenisPembayaran {
        $jenisPembayaran = $this->find($id);

        unset($data['tenant_id']);

        $jenisPembayaran->update($data);

        return $jenisPembayaran->fresh();
    }

    /**
     * Menghapus jenis pembayaran.
     */
    public function delete(int $id): bool
    {
        $jenisPembayaran = $this->find($id);

        return (bool) $jenisPembayaran->delete();
    }

    /**
     * Mengambil jenis pembayaran yang aktif saja.
     */
    public function active(): Collection
    {
        $tenant = $this->tenantContext->get();

        if (!$tenant) {
            throw new ModelNotFoundException(
                'Tenant aktif tidak ditemukan.'
            );
        }

        return JenisPembayaran::query()
            ->where('tenant_id', $tenant->id)
            ->where('aktif', true)
            ->orderBy('nama')
            ->get();
    }
}