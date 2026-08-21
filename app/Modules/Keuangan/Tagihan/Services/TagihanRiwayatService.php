<?php

namespace App\Modules\Keuangan\Tagihan\Services;

use App\Core\Tenant\Context\TenantContext;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use App\Modules\Keuangan\Tagihan\Models\TagihanRiwayat;

class TagihanRiwayatService
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    public function record(
        Tagihan $tagihan,
        string $aksi,
        array $dataSebelum,
        array $dataSesudah,
        ?string $keterangan = null
    ): TagihanRiwayat {
        $tenant = $this->tenantContext->get();

        return TagihanRiwayat::query()->create([
            'tenant_id' => $tenant->id,
            'tagihan_id' => $tagihan->id,
            'aksi' => $aksi,
            'data_sebelum' => $dataSebelum,
            'data_sesudah' => $dataSesudah,
            'keterangan' => $keterangan,
        ]);
    }
}