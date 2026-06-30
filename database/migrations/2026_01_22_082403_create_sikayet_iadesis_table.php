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
        Schema::create('sikayet_iadeleri', function (Blueprint $table) {
            $table->id();
            // İadeyi doğrudan Şikayet ID'sine bağlıyoruz
            $table->foreignId('musteri_sikayeti_id')->constrained('musteri_sikayetleri')->onDelete('cascade');
            
            // İşlemi yapan kişi (Log için)
            $table->foreignId('user_id')->constrained('users'); 
            
            // İade Detayları
            $table->string('urun_turu'); // Preform, Kapak, Hammadde...
            $table->decimal('miktar', 15, 2); // Adet veya Kg
            $table->string('birim'); // Adet, Kg, Koli, Palet
            $table->string('iade_sebebi'); // Renk hatası, Kırık...
            $table->text('aciklama')->nullable(); // Parti no vb.
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sikayet_iadesis');
    }
};
