<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('kode', 20);

            $table->string('nama', 50);

            $table->date('tanggal_mulai');

            $table->date('tanggal_selesai');

            $table->boolean('aktif')
                ->default(false);

            $table->timestamps();

            /*
             * Satu kode tahun ajaran boleh digunakan
             * oleh banyak sekolah, tetapi tidak boleh
             * duplikat dalam sekolah yang sama.
             */
            $table->unique([
                'tenant_id',
                'kode',
            ]);

            $table->index([
                'tenant_id',
                'aktif',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};