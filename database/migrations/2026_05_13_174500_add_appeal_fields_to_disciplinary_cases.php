<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            if (!Schema::hasColumn('disciplinary_cases', 'is_appealed')) {
                if (Schema::hasColumn('disciplinary_cases', 'oylama_aktif')) {
                    $table->boolean('is_appealed')->default(false)->after('oylama_aktif');
                } else {
                    $table->boolean('is_appealed')->default(false);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropColumn('is_appealed');
        });
    }
};
