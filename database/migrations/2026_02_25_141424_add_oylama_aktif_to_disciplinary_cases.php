<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            // 1. Önce eksik olan toplanti_tarihi sütununu ekle (yoksa)
            if (!Schema::hasColumn('disciplinary_cases', 'toplanti_tarihi')) {
                $table->dateTime('toplanti_tarihi')->nullable()->after('karar_dosyasi'); // Eğer 'karar_dosyasi' da yoksa burayı 'savunma_tarihi' yapabilirsiniz.
            }
            
            // 2. Sonra asıl eklemek istediğin oylama_aktif sütununu ekle
            if (!Schema::hasColumn('disciplinary_cases', 'oylama_aktif')) {
                $table->boolean('oylama_aktif')->default(false)->after('toplanti_tarihi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            if (Schema::hasColumn('disciplinary_cases', 'oylama_aktif')) {
                $table->dropColumn('oylama_aktif');
            }
            
            if (Schema::hasColumn('disciplinary_cases', 'toplanti_tarihi')) {
                $table->dropColumn('toplanti_tarihi');
            }
        });
    }
};