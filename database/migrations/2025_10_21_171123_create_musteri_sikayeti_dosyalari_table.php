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
        Schema::create('musteri_sikayeti_dosyalari', function (Blueprint $table) {
            $table->id();
            
            // Hangi şikayete ait olduğunu belirtir. Ana şikayet silinirse, dosyalar da silinir.
            $table->foreignId('musteri_sikayeti_id')->constrained('musteri_sikayetleri')->onDelete('cascade');
            
            // Dosyanın sunucudaki yolu (örn: sikayet_dosyalari/dosya_adi.pdf)
            $table->string('dosya_yolu');
            
            // Kullanıcının yüklediği dosyanın orijinal adı (örn: kanit.pdf)
            $table->string('orijinal_adi');

            // Dosyanın MIME tipi (örn: image/jpeg, application/pdf)
            $table->string('mime_tipi');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musteri_sikayeti_dosyalari');
    }
};
