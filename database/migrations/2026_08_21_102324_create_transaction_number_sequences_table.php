<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_number_sequences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->string('code', 50);

            $table->string('period', 6);

            $table->unsignedBigInteger('last_number')->default(0);

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'code',
                'period',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_number_sequences');
    }
};