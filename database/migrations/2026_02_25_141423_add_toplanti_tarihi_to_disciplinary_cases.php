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
            // Sütunların her birini tek tek varlık kontrolü yaparak ekleyelim
            if (!Schema::hasColumn('disciplinary_cases', 'yonetici_notu')) {
                // savunma_tarihi'nden hemen sonraya eklemeye çalışalım
                if (Schema::hasColumn('disciplinary_cases', 'savunma_tarihi')) {
                    $table->text('yonetici_notu')->nullable()->after('savunma_tarihi');
                } else {
                    $table->text('yonetici_notu')->nullable();
                }
            }
            
            if (!Schema::hasColumn('disciplinary_cases', 'karar_dosyasi')) {
                if (Schema::hasColumn('disciplinary_cases', 'yonetici_notu')) {
                    $table->string('karar_dosyasi')->nullable()->after('yonetici_notu');
                } else {
                    $table->string('karar_dosyasi')->nullable();
                }
            }

            if (!Schema::hasColumn('disciplinary_cases', 'toplanti_tarihi')) {
                if (Schema::hasColumn('disciplinary_cases', 'karar_dosyasi')) {
                    $table->dateTime('toplanti_tarihi')->nullable()->after('karar_dosyasi');
                } else {
                    $table->dateTime('toplanti_tarihi')->nullable();
                }
            }

            if (!Schema::hasColumn('disciplinary_cases', 'oylama_aktif')) {
                if (Schema::hasColumn('disciplinary_cases', 'toplanti_tarihi')) {
                    $table->boolean('oylama_aktif')->default(false)->after('toplanti_tarihi');
                } else {
                    $table->boolean('oylama_aktif')->default(false);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $columns = ['yonetici_notu', 'karar_dosyasi', 'toplanti_tarihi', 'oylama_aktif'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('disciplinary_cases', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
