<?php

namespace App\Modules\Keuangan\Tagihan\Services;

use App\Core\Tenant\Context\TenantContext;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TagihanService
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    /**
     * Mengambil seluruh tagihan tenant aktif.
     */
    public function getAll(): Collection
    {
        return Tagihan::query()
            ->with([
                'siswaTahun',
                'tarifPembayaran',
            ])
            ->latest('tanggal_tagihan')
            ->get();
    }

    /**
     * Mengambil tagihan berdasarkan ID.
     */
    public function find(int $id): Tagihan
    {
        return Tagihan::query()
            ->with([
                'siswaTahun',
                'tarifPembayaran',
            ])
            ->findOrFail($id);
    }

    /**
     * Membuat tagihan.
     *
     * Perhitungan diskon tidak dilakukan di sini.
     * GenerateTagihanService bertanggung jawab
     * untuk menghasilkan data tagihan.
     */
    public function create(array $data): Tagihan
    {
        return DB::transaction(function () use ($data) {
            $data['tenant_id'] = $this->tenantContext->id();

            return Tagihan::query()->create($data);
        });
    }

    /**
     * Memperbarui tagihan.
     */
    public function update(Tagihan $tagihan, array $data): Tagihan
    {
        return DB::transaction(function () use ($tagihan, $data) {
            $tagihan->update($data);

            return $tagihan->refresh();
        });
    }

    /**
     * Membatalkan tagihan.
     */
    public function cancel(
        Tagihan $tagihan,
        ?string $keterangan = null
    ): Tagihan {
        return DB::transaction(function () use ($tagihan, $keterangan) {
            $tagihan->update([
                'status' => 'BATAL',
                'keterangan' => $keterangan ?? $tagihan->keterangan,
            ]);

            return $tagihan->refresh();
        });
    }
}