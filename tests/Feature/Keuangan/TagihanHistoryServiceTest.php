<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use App\Modules\Keuangan\Tagihan\Models\TagihanRiwayat;
use App\Modules\Keuangan\Tagihan\Services\TagihanRiwayatService;
use Database\Seeders\DiskonPembayaranSeeder;
use Database\Seeders\JenisPembayaranSeeder;
use Database\Seeders\KelompokRombelSeeder;
use Database\Seeders\RombelSeeder;
use Database\Seeders\SiswaSeeder;
use Database\Seeders\SiswaTahunSeeder;
use Database\Seeders\TagihanSeeder;
use Database\Seeders\TarifPembayaranSeeder;
use Database\Seeders\TahunAjaranSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanRiwayatServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;

    protected Tagihan $tagihan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(TenantSeeder::class);

        $this->tenant = Tenant::query()
            ->where('code', 'DEMO')
            ->firstOrFail();

        app(TenantContext::class)->set($this->tenant);

        $this->seed([
            TahunAjaranSeeder::class,
            RombelSeeder::class,
            KelompokRombelSeeder::class,
            SiswaSeeder::class,
            SiswaTahunSeeder::class,
            JenisPembayaranSeeder::class,
            TarifPembayaranSeeder::class,
            DiskonPembayaranSeeder::class,
            TagihanSeeder::class,
        ]);

        $this->tagihan = Tagihan::query()->firstOrFail();
    }

    public function test_can_record_tagihan_history(): void
    {
        $dataSebelum = $this->tagihan->toArray();

        $dataSesudah = $dataSebelum;
        $dataSesudah['keterangan'] = 'Tagihan diperbarui';

        $riwayat = app(TagihanRiwayatService::class)->record(
            $this->tagihan,
            'UPDATE',
            $dataSebelum,
            $dataSesudah,
            'Perubahan keterangan tagihan'
        );

        $this->assertInstanceOf(
            TagihanRiwayat::class,
            $riwayat
        );

        $this->assertDatabaseHas('tagihan_riwayat', [
            'id' => $riwayat->id,
            'tenant_id' => $this->tenant->id,
            'tagihan_id' => $this->tagihan->id,
            'aksi' => 'UPDATE',
            'keterangan' => 'Perubahan keterangan tagihan',
        ]);

        $this->assertSame(
            $dataSebelum,
            $riwayat->data_sebelum
        );

        $this->assertSame(
            $dataSesudah,
            $riwayat->data_sesudah
        );
    }

    public function test_history_belongs_to_tagihan(): void
    {
        $riwayat = app(TagihanRiwayatService::class)->record(
            $this->tagihan,
            'UPDATE',
            ['status' => 'BELUM_BAYAR'],
            ['status' => 'LUNAS'],
            'Status berubah'
        );

        $this->assertNotNull($riwayat->tagihan);

        $this->assertSame(
            $this->tagihan->id,
            $riwayat->tagihan->id
        );
    }

    public function test_history_uses_current_tenant(): void
    {
        $riwayat = app(TagihanRiwayatService::class)->record(
            $this->tagihan,
            'UPDATE',
            ['status' => 'BELUM_BAYAR'],
            ['status' => 'SEBAGIAN'],
            'Pembayaran sebagian'
        );

        $this->assertSame(
            $this->tenant->id,
            $riwayat->tenant_id
        );
    }

    public function test_history_stores_json_snapshots(): void
    {
        $dataSebelum = [
            'nominal' => 150000,
            'status' => 'BELUM_BAYAR',
        ];

        $dataSesudah = [
            'nominal' => 120000,
            'status' => 'BELUM_BAYAR',
        ];

        $riwayat = app(TagihanRiwayatService::class)->record(
            $this->tagihan,
            'UPDATE',
            $dataSebelum,
            $dataSesudah,
            'Diskon diterapkan'
        );

        $riwayat->refresh();

        $this->assertSame(
            $dataSebelum,
            $riwayat->data_sebelum
        );

        $this->assertSame(
            $dataSesudah,
            $riwayat->data_sesudah
        );
    }

    public function test_can_record_cancel_history(): void
    {
        $riwayat = app(TagihanRiwayatService::class)->record(
            $this->tagihan,
            'CANCEL',
            ['status' => 'BELUM_BAYAR'],
            ['status' => 'BATAL'],
            'Tagihan dibatalkan'
        );

        $this->assertDatabaseHas('tagihan_riwayat', [
            'id' => $riwayat->id,
            'tenant_id' => $this->tenant->id,
            'tagihan_id' => $this->tagihan->id,
            'aksi' => 'CANCEL',
            'keterangan' => 'Tagihan dibatalkan',
        ]);
    }

    public function test_record_history_does_not_change_tagihan(): void
    {
        $before = $this->tagihan->fresh()->toArray();

        app(TagihanRiwayatService::class)->record(
            $this->tagihan,
            'UPDATE',
            ['status' => 'BELUM_BAYAR'],
            ['status' => 'SEBAGIAN'],
            'Riwayat saja'
        );

        $after = $this->tagihan->fresh()->toArray();

        $this->assertSame(
            $before,
            $after
        );
    }
}