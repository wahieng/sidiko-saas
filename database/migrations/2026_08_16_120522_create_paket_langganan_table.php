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
        Schema::create('paket_langganan', function (Blueprint $table) {

            $table->id();

            $table->string('kode', 50)
                ->unique();

            $table->string('nama', 100);

            $table->text('deskripsi')
                ->nullable();

            $table->decimal('harga', 15, 2)
                ->default(0);

            $table->enum('siklus_tagihan', [
                'bulanan',
                'semester',
                'tahunan',
            ])->default('bulanan');;

            $table->unsignedInteger('batas_siswa')
                ->nullable();

            $table->unsignedInteger('batas_pengguna')
                ->nullable();

            $table->unsignedInteger('batas_penyimpanan')
                ->nullable();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_langganan');
    }
};