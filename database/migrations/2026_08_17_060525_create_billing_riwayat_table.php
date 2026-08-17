<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_riwayat', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('billing_tagihan_id')
                ->nullable()
                ->constrained('billing_tagihan')
                ->nullOnDelete();

            $table->foreignId('billing_pembayaran_id')
                ->nullable()
                ->constrained('billing_pembayaran')
                ->nullOnDelete();

            $table->string('aksi', 50);

            $table->string('status_sebelumnya', 30)
                ->nullable();

            $table->string('status_sesudahnya', 30)
                ->nullable();

            $table->text('keterangan')
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'aksi']);
            $table->index('billing_tagihan_id');
            $table->index('billing_pembayaran_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_riwayat');
    }
};