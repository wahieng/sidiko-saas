<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();

            $table->enum('kode', [
                'ganjil',
                'genap',
            ]);

            $table->string('nama', 50);

            $table->date('tanggal_mulai');

            $table->date('tanggal_selesai');

            $table->boolean('aktif')
                ->default(false);

            $table->timestamps();

            $table->unique([
                'tahun_ajaran_id',
                'kode',
            ]);

            $table->index([
                'tahun_ajaran_id',
                'aktif',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester');
    }
};