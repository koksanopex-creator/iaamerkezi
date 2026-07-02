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
            $table->integer('periyot')->default(1)->after('siklik');
            $table->json('ayin_gunleri')->nullable()->after('haftanin_gunleri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayeti_yonetici_rapor_kurallari', function (Blueprint $table) {
            $table->dropColumn(['periyot', 'ayin_gunleri']);
        });
    }
};
