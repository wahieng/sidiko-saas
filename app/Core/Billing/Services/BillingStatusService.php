<?php

namespace App\Core\Billing\Services;

use App\Core\Billing\Models\PembayaranBilling;
use App\Core\Billing\Models\TagihanBilling;
use App\Core\Subscription\Services\SubscriptionService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BillingStatusService
{
    public const DRAFT = 'DRAFT';
    public const UNPAID = 'UNPAID';
    public const PARTIAL = 'PARTIAL';
    public const PAID = 'PAID';
    public const OVERDUE = 'OVERDUE';
    public const CANCELLED = 'CANCELLED';

    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {
    }

    public function sync(
        TagihanBilling $tagihan,
        ?PembayaranBilling $pembayaran = null
    ): TagihanBilling {
        return DB::transaction(function () use ($tagihan, $pembayaran) {

            $tagihan = TagihanBilling::query()
                ->lockForUpdate()
                ->findOrFail($tagihan->id);

            /*
            * Tagihan yang sudah dibatalkan adalah status final.
            *
            * Jangan pernah mengubah:
            * CANCELLED -> OVERDUE
            * CANCELLED -> PARTIAL
            * CANCELLED -> PAID
            */
            if ($tagihan->status === self::CANCELLED) {
                return $tagihan->refresh();
            }

            $total = (float) $tagihan->total;

            $dibayar = (float) $tagihan
                ->pembayaran()
                ->where('status', 'PAID')
                ->sum('jumlah');

            $statusLama = $tagihan->status;

            if ($dibayar >= $total && $total > 0) {
                $statusBaru = self::PAID;

            } elseif ($dibayar > 0) {
                $statusBaru = self::PARTIAL;

            } elseif (
                $tagihan->jatuh_tempo &&
                $tagihan->jatuh_tempo->isPast()
            ) {
                $statusBaru = self::OVERDUE;

            } else {
                $statusBaru = self::UNPAID;
            }

            if ($statusLama !== $statusBaru) {

                $tagihan->update([
                    'status' => $statusBaru,
                    'dibayar_pada' => $statusBaru === self::PAID
                        ? now()
                        : null,
                ]);

                $tagihan->riwayat()->create([
                    'billing_pembayaran_id' => $pembayaran?->id,
                    'aksi' => 'STATUS_CHANGED',
                    'status_sebelumnya' => $statusLama,
                    'status_sesudahnya' => $statusBaru,
                    'keterangan' =>
                        "Status berubah dari {$statusLama} menjadi {$statusBaru}.",
                ]);

                if ($statusBaru === self::PAID) {
                    $this->subscriptionService
                        ->aktifkanDariBilling($tagihan);
                }
            }

            return $tagihan->refresh();
        });
    }

    public function cancel(
        TagihanBilling $tagihan,
        ?string $keterangan = null
    ): TagihanBilling {

        if (
            $tagihan->pembayaran()
                ->where('status', 'PAID')
                ->exists()
        ) {
            throw new RuntimeException(
                'Tagihan yang sudah memiliki pembayaran tidak dapat dibatalkan.'
            );
        }

        $statusLama = $tagihan->status;

        $tagihan->update([
            'status' => self::CANCELLED,
        ]);

        $tagihan->riwayat()->create([
            'billing_pembayaran_id' => null,
            'aksi' => 'CANCELLED',
            'status_sebelumnya' => $statusLama,
            'status_sesudahnya' => self::CANCELLED,
            'keterangan' => $keterangan,
        ]);

        return $tagihan->refresh();
    }
}