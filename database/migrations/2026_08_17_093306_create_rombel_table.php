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

            $table->string('kode', 20)
                ->unique();

            $table->string('nama', 50);

            $table->unsignedTinyInteger('urutan');

            $table->boolean('aktif')
                ->default(true);

            $table->timestamps();

            $table->index('aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rombel');
    }
};