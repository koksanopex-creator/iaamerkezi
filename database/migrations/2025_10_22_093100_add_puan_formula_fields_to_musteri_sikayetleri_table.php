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
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Ek süre talebi için açıklama
            $table->text('ek_sure_talep_aciklamasi')->nullable()->after('musteri_ek_sure_talep_durumu');
            
            // Puanlama formülü için alanlar (kazanilan_puan'dan sonraya ekleyelim)
            $table->integer('etki_puani')->nullable()->after('kazanilan_puan');
            $table->integer('karmasiklik_puani')->nullable()->after('etki_puani');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->dropColumn(['ek_sure_talep_aciklamasi', 'etki_puani', 'karmasiklik_puani']);
        });
    }
};