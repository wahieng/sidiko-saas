<?php

namespace App\Core\Subscription\Console\Commands;

use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Services\SubscriptionRenewalService;
use App\Core\Tenant\Context\TenantContext;
use Illuminate\Console\Command;
use Throwable;

class GenerateSubscriptionRenewals extends Command
{
    protected $signature = 'subscription:generate-renewals';

    protected $description = 'Membuat billing renewal untuk subscription yang mendekati akhir periode';

    public function __construct(
        protected SubscriptionRenewalService $renewalService,
        protected TenantContext $tenantContext
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $jumlahDibuat = 0;
        $jumlahGagal = 0;

        Langganan::query()
            ->with('tenant')
            ->whereIn('status', [
                'trial',
                'active',
            ])
            ->whereNotNull('periode_berakhir')
            ->whereDate(
                'periode_berakhir',
                '<=',
                now()->addDays(7)->toDateString()
            )
            ->orderBy('id')
            ->each(function (Langganan $langganan) use (
                &$jumlahDibuat,
                &$jumlahGagal
            ) {
                try {
                    /*
                     * Set tenant context sebelum
                     * menjalankan proses billing.
                     */
                    $tenant = $langganan->tenant;

                    if (! $tenant) {
                        throw new \RuntimeException(
                            "Tenant #{$langganan->tenant_id} tidak ditemukan."
                        );
                    }

                    if (! $tenant->is_active) {
                        throw new \RuntimeException(
                            "Tenant #{$tenant->id} tidak aktif."
                        );
                    }

                    $this->tenantContext->set($tenant);

                    $billing = $this->renewalService
                        ->buatBillingRenewal($langganan);

                    if ($billing) {
                        $jumlahDibuat++;

                        $this->info(
                            "Billing {$billing->nomor_tagihan} dibuat " .
                            "untuk subscription #{$langganan->id}."
                        );
                    }
                } catch (Throwable $e) {
                    $jumlahGagal++;

                    $this->error(
                        "Gagal memproses subscription #{$langganan->id}: " .
                        $e->getMessage()
                    );
                } finally {
                    /*
                     * Wajib dibersihkan agar tenant
                     * sebelumnya tidak terbawa ke tenant berikutnya.
                     */
                    $this->tenantContext->clear();
                }
            });

        $this->info(
            "Selesai. {$jumlahDibuat} billing renewal dibuat, " .
            "{$jumlahGagal} gagal."
        );

        return $jumlahGagal > 0
            ? self::FAILURE
            : self::SUCCESS;
    }
}