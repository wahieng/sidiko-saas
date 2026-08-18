<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wali', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->cascadeOnDelete();

            $table->string('nama');
            $table->string('nik', 20)->nullable();
            $table->string('hubungan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->decimal('penghasilan', 15, 2)->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'siswa_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wali');
    }
};