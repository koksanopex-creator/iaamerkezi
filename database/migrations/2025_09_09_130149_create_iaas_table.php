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
        // =================================================================
        // İYİLEŞTİRMEYE AÇIK ALANLAR (İAA) TABLOSU
        // Kullanıcılar tarafından gönderilen tüm iyileştirme önerilerini tutar.
        // =================================================================
        Schema::create('iaas', function (Blueprint $table) {
            $table->id();
            $table->string('baslik'); // İAA Konusu / Başlığı
            $table->text('mevcut_durum'); // Problemin veya mevcut durumun tanımı
            $table->text('oneri'); // İyileştirme önerisi
            
            // --- Durum ve Atama Bilgileri ---
            $table->string('durum')->default('Onay Bekliyor'); // Örn: Onay Bekliyor, Havuzda, Atandı, Tamamlandı
            $table->integer('oncelik')->default(3); // Öncelik seviyesi (1: Yüksek, 5: Düşük gibi)

            // --- İlişkisel Anahtarlar (Foreign Keys) ---
            $table->foreignId('gonderen_user_id')->constrained('users'); // Öneriyi kimin gönderdiği
            $table->foreignId('bolum_id')->constrained('bolumler'); // Önerinin ilgili olduğu bölüm
            
            // Bu alanlar işlem yapıldıkça dolacak
            $table->foreignId('onaylayan_user_id')->nullable()->constrained('users'); // Havuza eklemeyi onaylayan yönetici
            $table->foreignId('atandi_user_id')->nullable()->constrained('users'); // İAA'nın atandığı ekip lideri
            
            // --- Yönetici Notları ---
            $table->text('yonetici_notu')->nullable(); // Onay/Red/Revize notu

            // --- Tarih Bilgileri ---
            $table->timestamp('onaylanma_tarihi')->nullable();
            $table->timestamp('atanma_tarihi')->nullable();
            $table->timestamp('tamamlanma_tarihi')->nullable();
            $table->timestamps(); // created_at (gönderilme tarihi), updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaas');
    }
};