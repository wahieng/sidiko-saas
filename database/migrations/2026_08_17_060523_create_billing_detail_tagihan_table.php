<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_detail_tagihan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('billing_tagihan_id')
                ->constrained('billing_tagihan')
                ->cascadeOnDelete();

            $table->string('deskripsi');

            $table->unsignedInteger('qty')
                ->default(1);

            $table->decimal('harga', 15, 2)
                ->default(0);

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->timestamps();

            $table->index('billing_tagihan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_detail_tagihan');
    }
};