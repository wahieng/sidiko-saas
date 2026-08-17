<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_tagihan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('langganan_id')
                ->constrained('langganan')
                ->restrictOnDelete();

            $table->foreignId('paket_langganan_id')
                ->constrained('paket_langganan')
                ->restrictOnDelete();

            $table->string('nomor_tagihan', 100)
                ->unique();

            $table->date('tanggal_tagihan');

            $table->date('jatuh_tempo');

            $table->date('periode_mulai');

            $table->date('periode_berakhir');

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->decimal('diskon', 15, 2)
                ->default(0);

            $table->decimal('total', 15, 2)
                ->default(0);

            $table->string('status', 30)
                ->default('UNPAID');

            $table->timestamp('dibayar_pada')
                ->nullable();

            $table->text('catatan')
                ->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['langganan_id']);
            $table->index(['jatuh_tempo']);
            $table->index(['periode_mulai', 'periode_berakhir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_tagihan');
    }
};