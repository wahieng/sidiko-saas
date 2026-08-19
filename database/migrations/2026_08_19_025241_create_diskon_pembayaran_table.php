<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diskon_pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->foreignId('tarif_pembayaran_id')
                ->constrained('tarif_pembayaran')
                ->restrictOnDelete();

            $table->enum('tipe_diskon', [
                'PERSEN',
                'NOMINAL',
            ]);

            $table->decimal('nilai', 15, 2);

            $table->text('keterangan')->nullable();

            $table->date('tanggal_mulai')->nullable();

            $table->date('tanggal_selesai')->nullable();

            $table->boolean('aktif')->default(true);

            $table->timestamps();

            $table->index([
                'tenant_id',
                'siswa_id',
            ]);

            $table->index([
                'tenant_id',
                'tarif_pembayaran_id',
            ]);

            $table->index('aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diskon_siswa');
    }
};