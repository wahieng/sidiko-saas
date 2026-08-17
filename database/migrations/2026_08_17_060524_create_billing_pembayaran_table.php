<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('billing_tagihan_id')
                ->constrained('billing_tagihan')
                ->restrictOnDelete();

            $table->string('nomor_pembayaran', 100)
                ->unique();

            $table->date('tanggal_pembayaran');

            $table->decimal('jumlah', 15, 2);

            $table->string('metode', 30);

            $table->string('referensi', 150)
                ->nullable();

            $table->string('status', 30)
                ->default('PENDING');

            $table->text('catatan')
                ->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('billing_tagihan_id');
            $table->index('tanggal_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_pembayaran');
    }
};