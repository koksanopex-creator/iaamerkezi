<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arabuluculuk_payments', function (Blueprint $table) {
            // Eğer created_by yoksa ekle
            if (!Schema::hasColumn('arabuluculuk_payments', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->after('finance_onay_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arabuluculuk_payments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};