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
        Schema::table('musteri_sikayeti_yonetici_rapor_kurallari', function (Blueprint $table) {
            $table->json('alicilar')->nullable()->after('aktif');
            $table->string('rapor_kapsami')->default('tum_kurul')->after('alicilar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayeti_yonetici_rapor_kurallari', function (Blueprint $table) {
            $table->dropColumn('alicilar');
            $table->dropColumn('rapor_kapsami');
        });
    }
};
