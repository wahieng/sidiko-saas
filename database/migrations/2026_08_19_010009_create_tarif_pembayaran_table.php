<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarif_pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('jenis_pembayaran_id')
                ->constrained('jenis_pembayaran')
                ->cascadeOnDelete();

            $table->foreignId('kelompok_rombel_id')
                ->constrained('kelompok_rombel')
                ->cascadeOnDelete();

            $table->decimal('nominal', 15, 2);

            $table->text('keterangan')
                ->nullable();

            $table->boolean('aktif')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'jenis_pembayaran_id',
                'kelompok_rombel_id',
            ], 'tarif_pembayaran_tenant_jenis_kelompok_unique');

            $table->index([
                'tenant_id',
                'kelompok_rombel_id',
                'aktif',
            ], 'tarif_pembayaran_tenant_kelompok_aktif_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarif_pembayaran');
    }
};