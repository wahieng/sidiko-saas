<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rombel', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('kode', 20);

            $table->string('nama', 50);

            $table->unsignedTinyInteger('urutan');

            $table->boolean('aktif')
                ->default(true);

            $table->timestamps();

            /*
             * Kode rombel boleh sama di sekolah berbeda,
             * tetapi tidak boleh duplikat dalam satu sekolah.
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
        Schema::dropIfExists('rombel');
    }
};