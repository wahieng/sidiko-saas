<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diskon_pembayaran', function (Blueprint $table) {
            $table->id();

            /*
             * Tenant pemilik data.
             */
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            /*
             * Siswa dalam konteks tahun ajaran.
             *
             * SiswaTahun sudah mewakili:
             * - siswa
             * - tahun ajaran
             * - kelompok rombel
             */
            $table->foreignId('siswa_tahun_id')
                ->constrained('siswa_tahun')
                ->restrictOnDelete();

            /*
             * Tarif pembayaran yang mendapatkan diskon.
             */
            $table->foreignId('tarif_pembayaran_id')
                ->constrained('tarif_pembayaran')
                ->restrictOnDelete();

            /*
             * Jenis diskon.
             */
            $table->enum('tipe_diskon', [
                'PERSEN',
                'NOMINAL',
            ]);

            /*
             * Nilai diskon.
             *
             * PERSEN   = persentase
             * NOMINAL  = nominal rupiah
             */
            $table->decimal('nilai', 15, 2);

            /*
             * Periode berlaku.
             */
            $table->date('tanggal_mulai')
                ->nullable();

            $table->date('tanggal_selesai')
                ->nullable();

            /*
             * Status diskon.
             */
            $table->boolean('is_active')
                ->default(true);

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

            /*
             * Index pencarian.
             */
            $table->index([
                'tenant_id',
                'siswa_tahun_id',
            ]);

            $table->index([
                'tenant_id',
                'tarif_pembayaran_id',
            ]);

            $table->index([
                'tenant_id',
                'is_active',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskon_pembayaran');
    }
};