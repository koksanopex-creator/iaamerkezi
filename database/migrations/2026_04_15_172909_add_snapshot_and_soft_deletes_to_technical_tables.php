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
        // 1. Snapshot kolonları (Geçmiş verilerin korunması için)
        Schema::table('sikayet_teknik_detaylari', function (Blueprint $table) {
            $table->string('machine_name')->nullable()->after('machine_id');
            $table->string('genel_hammadde_name')->nullable()->after('genel_hammadde_id');
            $table->string('urun_versiyonu_name')->nullable()->after('urun_versiyonu_id');
        });

        // 2. SoftDelete eklemeleri (İlişkilerin kopmaması için)
        Schema::table('genel_hammaddeler', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('urun_versiyonlari', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sikayet_teknik_detaylari', function (Blueprint $table) {
            $table->dropColumn(['machine_name', 'genel_hammadde_name', 'urun_versiyonu_name']);
        });

        Schema::table('genel_hammaddeler', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('urun_versiyonlari', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
