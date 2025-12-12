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
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            // Hesaplanan puandan hemen sonraya ekleyelim
            $table->string('sistem_oneri_ceza')->nullable()->after('hesaplanan_puan');
        });
    }

    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropColumn('sistem_oneri_ceza');
        });
    }
};
