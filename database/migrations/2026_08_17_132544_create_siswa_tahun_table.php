<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa_tahun', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->restrictOnDelete();

            $table->foreignId('kelompok_rombel_id')
                ->constrained('kelompok_rombel')
                ->restrictOnDelete();

            $table->enum('status', [
                'AKTIF',
                'LULUS',
                'PINDAH',
                'KELUAR',
            ])->default('AKTIF');

            $table->date('tanggal_masuk')->nullable();

            $table->date('tanggal_keluar')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->unique(
                ['siswa_id', 'tahun_ajaran_id'],
                'siswa_tahun_unique'
            );

            $table->index(
                ['tahun_ajaran_id', 'status'],
                'siswa_tahun_tahun_status_index'
            );

            $table->index(
                ['kelompok_rombel_id', 'status'],
                'siswa_tahun_kelompok_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_tahun');
    }
};