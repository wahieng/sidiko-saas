<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('langganan', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | TENANT
            |--------------------------------------------------------------------------
            */

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();


            /*
            |--------------------------------------------------------------------------
            | PAKET LANGGANAN
            |--------------------------------------------------------------------------
            */

            $table->foreignId('paket_langganan_id')
                ->constrained('paket_langganan')
                ->restrictOnDelete();


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $table->enum('status', [
                'trial',
                'active',
                'past_due',
                'cancelled',
                'expired',
                'suspended',
            ])->default('trial');


            /*
            |--------------------------------------------------------------------------
            | PERIODE LANGGANAN
            |--------------------------------------------------------------------------
            */

            $table->timestamp('mulai_pada')
                ->nullable();

            $table->timestamp('trial_berakhir_pada')
                ->nullable();

            $table->date('periode_mulai')
                ->nullable();

            $table->date('periode_berakhir')
                ->nullable();

            $table->timestamp('dibatalkan_pada')
                ->nullable();


            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index([
                'tenant_id',
                'status',
            ]);
        });
    }

    /**
     * Membatalkan migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('langganan');
    }
};