<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('rapor_kurallari', function (Blueprint $table) {
        $table->id();
        $table->string('baslik'); // Örn: "Yönetim Kurulu Haftalık Rapor"
        
        // Zamanlama Ayarları
        $table->string('periyot')->default('gunluk'); // gunluk, haftalik, aylik
        $table->time('gonderim_saati')->default('09:00'); 
        
        // KİME GİDECEK? (JSON olarak tutacağız: roller, user_id'ler, harici mailler)
        // Örn: { "roller": [1, 3], "users": [5, 12], "emails": "a@test.com" }
        $table->json('alicilar'); 
        
        // NE GİDECEK? (İçerik Seçimi)
        // Örn: { "sikayet": true, "iaa": false, "disiplin": true ... }
        $table->json('icerik_ayarlari');
        
        $table->boolean('aktif')->default(true);
        $table->timestamp('son_gonderim_tarihi')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapor_kuralis');
    }
};
