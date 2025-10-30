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
        Schema::table('takimlar', function (Blueprint $table) {
            // "kurallar" kolonundan sonra, varsayılan değeri 0 olan yeni bir ondalıklı sayı kolonu ekliyoruz.
            // 15 karakter toplam, 2'si ondalık hane için (Örn: 9999999999999.99)
            $table->decimal('toplam_puan', 15, 2)->default(0)->after('kurallar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('takimlar', function (Blueprint $table) {
            // Eğer bu değişikliği geri alırsak, eklediğimiz kolonu sil.
            $table->dropColumn('toplam_puan');
        });
    }
};