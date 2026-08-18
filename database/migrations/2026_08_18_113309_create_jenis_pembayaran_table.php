<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('kode', 50);
            $table->string('nama', 100);

            $table->enum('kategori', [
                'BULANAN',
                'SEMESTER',
                'TAHUNAN',
                'SEKALI',
            ]);

            $table->text('keterangan')->nullable();

            $table->boolean('aktif')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'kode'],
                'jenis_pembayaran_tenant_kode_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pembayaran');
    }
};