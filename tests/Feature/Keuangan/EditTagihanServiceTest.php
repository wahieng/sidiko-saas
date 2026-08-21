<?php

namespace Tests\Feature\Keuangan;

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\Tagihan\Models\Tagihan;
use App\Modules\Keuangan\Tagihan\Models\TagihanRiwayat;
use App\Modules\Keuangan\Tagihan\Services\EditTagihanService;
use App\Modules\Keuangan\JenisPembayaran\Models\JenisPembayaran;
use App\Modules\Keuangan\TarifPembayaran\Models\TarifPembayaran;
use App\Modules\Siswa\SiswaTahun\Models\SiswaTahun;
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

class EditTagihanServiceTest extends TestCase
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

        $this->tagihan = Tagihan::query()
            ->where('status', 'BELUM_BAYAR')
            ->firstOrFail();
    }

    public function test_can_edit_unpaid_tagihan(): void
    {
        $result = app(EditTagihanService::class)->update(
            $this->tagihan,
            [
                'keterangan' => 'Tagihan diperbarui',
            ]
        );

        $this->assertSame(
            'Tagihan diperbarui',
            $result->keterangan
        );

        $this->assertDatabaseHas('tagihan', [
            'id' => $this->tagihan->id,
            'keterangan' => 'Tagihan diperbarui',
        ]);
    }

    public function test_edit_creates_history(): void
    {
        app(EditTagihanService::class)->update(
            $this->tagihan,
            [
                'keterangan' => 'Tagihan diperbarui',
            ]
        );

        $this->assertDatabaseHas('tagihan_riwayat', [
            'tenant_id' => $this->tenant->id,
            'tagihan_id' => $this->tagihan->id,
            'aksi' => 'UPDATE',
        ]);
    }

    public function test_edit_does_not_change_nominal_when_only_keterangan_is_updated(): void
    {
        $nominal = $this->tagihan->nominal;

        app(EditTagihanService::class)->update(
            $this->tagihan,
            [
                'keterangan' => 'Keterangan baru',
            ]
        );

        $this->assertSame(
            $nominal,
            $this->tagihan->fresh()->nominal
        );
    }

    public function test_partially_paid_tagihan_cannot_be_edited(): void
    {
        $tagihan = $this->tagihan;

        $tagihan->update([
            'status' => 'SEBAGIAN',
            'jumlah_dibayar' => 50000,
            'sisa_tagihan' => $tagihan->nominal - 50000,
        ]);

        $this->expectException(\RuntimeException::class);

        app(EditTagihanService::class)->update(
            $tagihan->fresh(),
            [
                'keterangan' => 'Tidak boleh diubah',
            ]
        );
    }

    public function test_paid_tagihan_cannot_be_edited(): void
    {
        $tagihan = $this->tagihan;

        $tagihan->update([
            'status' => 'LUNAS',
            'jumlah_dibayar' => $tagihan->nominal,
            'sisa_tagihan' => 0,
        ]);

        $this->expectException(\RuntimeException::class);

        app(EditTagihanService::class)->update(
            $tagihan->fresh(),
            [
                'keterangan' => 'Tidak boleh diubah',
            ]
        );
    }

    public function test_cancelled_tagihan_cannot_be_edited(): void
    {
        $tagihan = $this->tagihan;

        $tagihan->update([
            'status' => 'BATAL',
        ]);

        $this->expectException(\RuntimeException::class);

        app(EditTagihanService::class)->update(
            $tagihan->fresh(),
            [
                'keterangan' => 'Tidak boleh diubah',
            ]
        );
    }

    public function test_tagihan_tenant_lain_tidak_dapat_diedit(): void
    {
        $tagihan = $this->tagihan;

        $otherTenant = Tenant::query()->create([
            'name' => 'Other School',
            'code' => 'OTHER',
            'slug' => 'other-school',
            'email' => 'other@school.test',
            'phone' => '0800000001',
            'address' => 'Other Address',
            'is_active' => true,
        ]);

        app(TenantContext::class)->set($otherTenant);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $tenantTagihan = Tagihan::query()->findOrFail($tagihan->id);

        app(EditTagihanService::class)->update(
            $tenantTagihan,
            [
                'keterangan' => 'Tidak boleh diedit',
            ]
        );
    }
}