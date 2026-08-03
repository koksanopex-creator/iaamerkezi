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
        Schema::table('rapor_kurallari', function (Blueprint $table) {
            $table->string('disiplin_kapsam')->default('tum_veriler')->after('icerik_ayarlari')->comment('tum_veriler, kendi_bolumu');
            $table->json('disiplin_suc_kategorileri')->nullable()->after('disiplin_kapsam')->comment('Null ise tüm suçlar, değilse seçili kategori id array');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rapor_kurallari', function (Blueprint $table) {
            $table->dropColumn('disiplin_kapsam');
            $table->dropColumn('disiplin_suc_kategorileri');
        });
    }
};
