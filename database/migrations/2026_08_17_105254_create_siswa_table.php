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

            /*
            |--------------------------------------------------------------------------
            | Tenant
            |--------------------------------------------------------------------------
            */

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identitas Utama
            |--------------------------------------------------------------------------
            */

            $table->string('nis', 50)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('no_kk', 20)->nullable();

            $table->string('nama');
            $table->string('nama_panggilan')->nullable();

            $table->string('jenis_kelamin', 1);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Kontak
            |--------------------------------------------------------------------------
            */

            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Alamat
            |--------------------------------------------------------------------------
            */

            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informasi Tambahan
            |--------------------------------------------------------------------------
            */

            $table->unsignedTinyInteger('anak_ke')->nullable();
            $table->unsignedTinyInteger('jumlah_saudara')->nullable();
            $table->string('jenis_tinggal')->nullable();
            $table->string('transportasi')->nullable();
            $table->string('kebutuhan_khusus')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Foto
            |--------------------------------------------------------------------------
            */

            $table->string('foto')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index / Unique
            |--------------------------------------------------------------------------
            |
            | NIS/NISN/NIK harus unik dalam satu tenant,
            | bukan unik secara global.
            |
            */

            $table->unique(
                ['tenant_id', 'nis'],
                'siswa_tenant_nis_unique'
            );

            $table->unique(
                ['tenant_id', 'nisn'],
                'siswa_tenant_nisn_unique'
            );

            $table->unique(
                ['tenant_id', 'nik'],
                'siswa_tenant_nik_unique'
            );

            $table->index(
                ['tenant_id', 'nama'],
                'siswa_tenant_nama_index'
            );
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