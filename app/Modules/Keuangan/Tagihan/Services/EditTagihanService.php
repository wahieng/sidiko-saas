<?php

namespace App\Modules\Keuangan\Tagihan\Services;

use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EditTagihanService
{
    public function __construct(
        protected TagihanRiwayatService $riwayatService
    ) {
    }

    public function update(
        Tagihan $tagihan,
        array $data
    ): Tagihan {
        return DB::transaction(function () use ($tagihan, $data) {
            $tagihan->refresh();

            if ($tagihan->status !== 'BELUM_BAYAR') {
                throw new RuntimeException(
                    'Tagihan hanya dapat diedit ketika status BELUM_BAYAR.'
                );
            }

            $dataSebelum = $tagihan->toArray();

            $tagihan->update($data);

            $tagihan->refresh();

            $dataSesudah = $tagihan->toArray();

            $this->riwayatService->record(
                $tagihan,
                'UPDATE',
                $dataSebelum,
                $dataSesudah,
                'Tagihan diperbarui'
            );

            return $tagihan;
        });
    }
}