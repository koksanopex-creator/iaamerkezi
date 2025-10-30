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
            // Proje köprüsü için iaa_id sütununu ekliyoruz
            // 'id' sütunundan sonra (veya 'atanan_cozum_takimi_id' gibi bir yerden sonra) ekleyebiliriz
            $table->unsignedBigInteger('iaa_id')->nullable()->after('id');
            
            // Foreign key (ilişki) tanımlaması
            $table->foreign('iaa_id')
                  ->references('id')
                  ->on('iaas') // iaa_db (12).sql dosyasındaki 'iaas' tablosuna bağlanır
                  ->onDelete('set null'); // Eğer IAA projesi silinirse, şikayetteki bu alanı null yap
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Önce foreign key'i kaldır
            $table->dropForeign(['iaa_id']);
            // Sonra sütunu kaldır
            $table->dropColumn('iaa_id');
        });
    }
};