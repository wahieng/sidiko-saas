<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            /*
             * Siswa dalam konteks tahun ajaran.
             */
            $table->foreignId('siswa_tahun_id')
                ->constrained('siswa_tahun')
                ->restrictOnDelete();

            /*
             * Tarif yang menjadi sumber tagihan.
             */
            $table->foreignId('tarif_pembayaran_id')
                ->constrained('tarif_pembayaran')
                ->restrictOnDelete();

            /*
             * Nomor tagihan.
             */
            $table->string('nomor_tagihan', 100);

            /*
             * Snapshot nominal sebelum diskon.
             */
            $table->decimal('nominal_awal', 15, 2);

            /*
             * Snapshot diskon ketika tagihan dibuat.
             */
            $table->enum('tipe_diskon', [
                'PERSEN',
                'NOMINAL',
            ])->nullable();

            $table->decimal('nilai_diskon', 15, 2)
                ->default(0);

            $table->decimal('nominal_diskon', 15, 2)
                ->default(0);

            /*
             * Nominal akhir tagihan setelah diskon.
             */
            $table->decimal('nominal', 15, 2);

            /*
             * Total pembayaran yang sudah masuk.
             */
            $table->decimal('jumlah_dibayar', 15, 2)
                ->default(0);

            /*
             * Sisa tagihan.
             */
            $table->decimal('sisa_tagihan', 15, 2);

            $table->date('tanggal_tagihan');

            $table->date('tanggal_jatuh_tempo')
                ->nullable();

            $table->enum('status', [
                'BELUM_BAYAR',
                'SEBAGIAN',
                'LUNAS',
                'BATAL',
            ])->default('BELUM_BAYAR');

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

            /*
             * Nomor tagihan unik dalam tenant.
             */
            $table->unique([
                'tenant_id',
                'nomor_tagihan',
            ]);

            /*
             * Query tagihan siswa.
             */
            $table->index([
                'tenant_id',
                'siswa_tahun_id',
            ]);

            /*
             * Query berdasarkan tarif.
             */
            $table->index([
                'tenant_id',
                'tarif_pembayaran_id',
            ]);

            /*
             * Filter status.
             */
            $table->index([
                'tenant_id',
                'status',
            ]);

            /*
             * Filter periode tagihan.
             */
            $table->index([
                'tenant_id',
                'tanggal_tagihan',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};