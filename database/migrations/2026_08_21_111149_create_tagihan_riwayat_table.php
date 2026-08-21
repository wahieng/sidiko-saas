<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_riwayat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('tagihan_id')
                ->constrained('tagihan')
                ->cascadeOnDelete();

            $table->string('aksi', 50);

            $table->json('data_sebelum')
                ->nullable();

            $table->json('data_sesudah')
                ->nullable();

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'tagihan_id',
            ]);

            $table->index([
                'tenant_id',
                'aksi',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_riwayat');
    }
};