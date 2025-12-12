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
            
            // 1. Savunma Açıklaması yoksa ekle
            if (!Schema::hasColumn('disciplinary_cases', 'savunma_aciklamasi')) {
                $table->text('savunma_aciklamasi')->nullable()->after('kanit_dosyalari');
            }

            // 2. Savunma Dosyaları yoksa ekle
            if (!Schema::hasColumn('disciplinary_cases', 'savunma_dosyalari')) {
                $table->json('savunma_dosyalari')->nullable()->after('savunma_aciklamasi');
            }

            // 3. Savunma Tarihi yoksa ekle (Hata veren yer burasıydı, kontrol ekledik)
            if (!Schema::hasColumn('disciplinary_cases', 'savunma_tarihi')) {
                $table->dateTime('savunma_tarihi')->nullable()->after('savunma_dosyalari');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            // Sütunları silerken de kontrol edelim
            if (Schema::hasColumn('disciplinary_cases', 'savunma_aciklamasi')) {
                $table->dropColumn('savunma_aciklamasi');
            }
            if (Schema::hasColumn('disciplinary_cases', 'savunma_dosyalari')) {
                $table->dropColumn('savunma_dosyalari');
            }
            if (Schema::hasColumn('disciplinary_cases', 'savunma_tarihi')) {
                $table->dropColumn('savunma_tarihi');
            }
        });
    }
};