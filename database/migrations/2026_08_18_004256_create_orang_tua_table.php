<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id();

            // Tenant
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // Siswa
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            // Hubungan dengan siswa
            $table->enum('hubungan', [
                'AYAH',
                'IBU',
            ]);

            // Identitas
            $table->string('nama');
            $table->string('nik', 20)->nullable();
            $table->string('no_kk', 20)->nullable();

            // Data pribadi
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();

            // Pendidikan & pekerjaan
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->decimal('penghasilan', 15, 2)->nullable();

            // Kontak
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Alamat
            $table->text('alamat')->nullable();

            // Keterangan tambahan
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Satu siswa maksimal satu ayah dan satu ibu
            $table->unique([
                'siswa_id',
                'hubungan',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};