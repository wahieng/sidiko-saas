<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_detail_tagihan', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();
            $table->index(['tenant_id', 'billing_tagihan_id']);
        });
    }

    public function down(): void
    {
        Schema::table('billing_detail_tagihan', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id', 'billing_tagihan_id']);
            $table->dropColumn('tenant_id');
        });
    }
};