<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('fitur_paket', function (Blueprint $table) {

            $table->id();

            $table->foreignId('paket_langganan_id')
                ->constrained('paket_langganan')
                ->cascadeOnDelete();

            $table->string('kode_fitur', 100);

            $table->boolean('aktif')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'paket_langganan_id',
                'kode_fitur',
            ]);
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('fitur_paket');
    }
};