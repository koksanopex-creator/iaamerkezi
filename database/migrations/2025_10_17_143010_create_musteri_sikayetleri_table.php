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
        Schema::create('musteri_sikayetleri', function (Blueprint $table) {
            $table->id();

            // Müşteri ve Şikayet Bilgileri
            $table->string('musteri_adi');
            $table->string('musteri_iletisim')->nullable();
            $table->string('musteri_sikayet_konusu'); 
            $table->text('musteri_sikayet_detayi'); 
            $table->string('musteri_urun_veya_hizmet')->nullable(); 
            $table->string('musteri_barkod_resim_yolu')->nullable(); 
            $table->date('musteri_sikayet_tarihi'); 

            // Süreç ve Durum Bilgileri
            $table->string('musteri_durum')->default('Yeni'); 
            $table->string('musteri_oncelik')->default('Normal'); 

            // Atama Bilgileri
            $table->foreignId('atanan_cozum_takimi_id')->nullable()->constrained('takimlar')->onDelete('set null');

            // Onay ve Kapanış
            $table->text('musteri_cozum_notlari')->nullable(); 
            $table->timestamp('musteri_onay_tarihi')->nullable(); 
            $table->timestamp('kurul_onay_tarihi')->nullable();
            $table->foreignId('olusturan_kurul_uyesi_id')->nullable()->constrained('users')->onDelete('set null');

            // Sayaç ve Süre Yönetimi
            $table->timestamp('musteri_cozum_son_tarihi')->nullable(); 
            $table->string('musteri_ek_sure_talep_durumu')->nullable(); 

            // Puanlama
            $table->decimal('musteri_puan', 8, 2)->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musteri_sikayetleri');
    }
};