<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();

            // Identitas utama
            $table->string('nis', 50)->nullable()->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('no_kk', 20)->nullable();

            $table->string('nama');
            $table->string('nama_panggilan')->nullable();

            $table->string('jenis_kelamin', 1);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();

            // Kontak
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            // Alamat
            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();

            // Informasi tambahan
            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->string('jenis_tinggal')->nullable();
            $table->string('transportasi')->nullable();
            $table->string('kebutuhan_khusus')->nullable();

            // Foto profil siswa
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};