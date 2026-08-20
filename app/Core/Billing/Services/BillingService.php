<?php

namespace App\Core\Billing\Services;

use App\Core\Billing\Models\TagihanBilling;
use App\Core\Subscription\Models\Langganan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BillingService
{
    public function create(array $data): TagihanBilling
    {
        return DB::transaction(function () use ($data) {
            $langganan = Langganan::query()
                ->with('paket')
                ->findOrFail($data['langganan_id']);

            $paket = $langganan->paket;

            if (! $paket) {
                throw new RuntimeException(
                    'Paket subscription tidak ditemukan.'
                );
            }

            if (
                isset($data['paket_langganan_id']) &&
                (int) $data['paket_langganan_id'] !== (int) $paket->id
            ) {
                throw new RuntimeException(
                    'Paket billing tidak sesuai dengan subscription.'
                );
            }

            $tagihan = TagihanBilling::create([
                'langganan_id' => $langganan->id,

                'paket_langganan_id' => $paket->id,

                'nomor_tagihan' => $data['nomor_tagihan']
                    ?? $this->generateNomorTagihan(),

                'tanggal_tagihan' => $data['tanggal_tagihan'],

                'jatuh_tempo' => $data['jatuh_tempo'],

                'periode_mulai' => $data['periode_mulai'],

                'periode_berakhir' => $data['periode_berakhir'],

                'subtotal' => $data['subtotal'] ?? 0,

                'diskon' => $data['diskon'] ?? 0,

                'total' => $data['total'] ?? 0,

                'status' => $data['status'] ?? 'UNPAID',

                'catatan' => $data['catatan'] ?? null,
            ]);

            if (! empty($data['detail'])) {
                $this->createDetails(
                    $tagihan,
                    $data['detail']
                );
            }

            return $tagihan->load([
                'detail',
                'langganan',
                'paket',
            ]);
        });
    }

    public function update(
        TagihanBilling $tagihan,
        array $data
    ): TagihanBilling {
        $tagihan->update([
            'tanggal_tagihan' => $data['tanggal_tagihan']
                ?? $tagihan->tanggal_tagihan,

            'jatuh_tempo' => $data['jatuh_tempo']
                ?? $tagihan->jatuh_tempo,

            'periode_mulai' => $data['periode_mulai']
                ?? $tagihan->periode_mulai,

            'periode_berakhir' => $data['periode_berakhir']
                ?? $tagihan->periode_berakhir,

            'subtotal' => $data['subtotal']
                ?? $tagihan->subtotal,

            'diskon' => $data['diskon']
                ?? $tagihan->diskon,

            'total' => $data['total']
                ?? $tagihan->total,

            'catatan' => $data['catatan']
                ?? $tagihan->catatan,
        ]);

        return $tagihan->refresh();
    }

    protected function createDetails(
        TagihanBilling $tagihan,
        array $details
    ): void {
        foreach ($details as $detail) {
            $qty = $detail['qty'] ?? 1;
            $harga = $detail['harga'] ?? 0;

            $tagihan->detail()->create([
                'deskripsi' => $detail['deskripsi'],
                'qty' => $qty,
                'harga' => $harga,
                'subtotal' => $detail['subtotal']
                    ?? ($qty * $harga),
            ]);
        }
    }

    public function all()
    {
        return TagihanBilling::query()->get();
    }

    public function find(int $id)
    {
        return TagihanBilling::query()->findOrFail($id);
    }
    
    protected function generateNomorTagihan(): string
    {
        return 'BILL-' .
            now()->format('Ym') . '-' .
            strtoupper(Str::random(8));
    }
}