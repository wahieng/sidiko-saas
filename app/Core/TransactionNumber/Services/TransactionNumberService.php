<?php

namespace App\Core\TransactionNumber\Services;

use App\Core\Tenant\Context\TenantContext;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransactionNumberService
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    /**
     * Generate nomor transaksi berikutnya.
     */
    public function generate(
        string $code,
        ?string $period = null
    ): string {
        $tenant = $this->tenantContext->require();

        $period ??= now()->format('Ym');

        $formats = config('transaction_number.formats');

        if (! isset($formats[$code])) {
            throw new RuntimeException(
                "Format nomor transaksi untuk kode {$code} tidak ditemukan."
            );
        }

        return DB::transaction(function () use (
            $tenant,
            $code,
            $period,
            $formats
        ) {
            $sequence = DB::table(
                'transaction_number_sequences'
            )
                ->where('tenant_id', $tenant->id)
                ->where('code', $code)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                DB::table(
                    'transaction_number_sequences'
                )->insert([
                    'tenant_id' => $tenant->id,
                    'code' => $code,
                    'period' => $period,
                    'last_number' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $number = 1;
            } else {
                $number = $sequence->last_number + 1;

                DB::table(
                    'transaction_number_sequences'
                )
                    ->where('id', $sequence->id)
                    ->update([
                        'last_number' => $number,
                        'updated_at' => now(),
                    ]);
            }

            return str_replace(
                [
                    '{YYYYMM}',
                    '{NUMBER}',
                ],
                [
                    $period,
                    str_pad(
                        (string) $number,
                        (int) config(
                            'transaction_number.padding',
                            6
                        ),
                        '0',
                        STR_PAD_LEFT
                    ),
                ],
                $formats[$code]
            );
        });
    }
}