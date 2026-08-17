<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelompok_rombel', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->cascadeOnDelete();

            $table->foreignId('rombel_id')
                ->constrained('rombel')
                ->restrictOnDelete();

            $table->string('kode', 20);

            $table->string('nama', 50);

            $table->unsignedTinyInteger('urutan')
                ->default(1);

            $table->boolean('aktif')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'tahun_ajaran_id',
                'rombel_id',
                'kode',
            ]);

            $table->index([
                'tahun_ajaran_id',
                'rombel_id',
                'aktif',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelompok_rombel');
    }
};