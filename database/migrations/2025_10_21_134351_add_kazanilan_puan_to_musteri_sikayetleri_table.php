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
            // 'musteri_puan' sütunundan sonra 'kazanilan_puan' adında yeni bir sütun ekle.
            // Varsayılan değeri 0 olsun.
            $table->integer('kazanilan_puan')->default(0)->after('musteri_puan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Eğer migration geri alınırsa bu sütunu kaldır.
            $table->dropColumn('kazanilan_puan');
        });
    }
};
