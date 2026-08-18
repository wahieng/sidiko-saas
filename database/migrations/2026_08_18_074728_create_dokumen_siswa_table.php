<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_siswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->string('jenis_dokumen', 100);

            $table->string('nama_file');

            $table->string('nama_asli');

            $table->string('path');

            $table->string('disk', 50)
                ->default('local');

            $table->string('mime_type', 100)
                ->nullable();

            $table->unsignedBigInteger('ukuran')
                ->nullable();

            $table->text('keterangan')
                ->nullable();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'siswa_id',
            ]);

            $table->index([
                'tenant_id',
                'jenis_dokumen',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_siswa');
    }
};