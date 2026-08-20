<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('semester', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->index(['tenant_id', 'tahun_ajaran_id', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::table('semester', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id', 'tahun_ajaran_id', 'aktif']);
            $table->dropColumn('tenant_id');
        });
    }
};