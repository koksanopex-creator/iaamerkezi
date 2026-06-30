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
        // 1. ŞİKAYET HATIRLATMALARI (ANA TABLO)
        Schema::create('sikayet_hatirlatmalari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musteri_sikayeti_id')->constrained('musteri_sikayetleri')->onDelete('cascade');
            $table->foreignId('gonderen_user_id')->constrained('users');
            $table->enum('durum', ['bilgi_girisi_bekleniyor', 'bilgi_girildi', 'musteri_ikna_oldu', 'kapatildi'])->default('bilgi_girisi_bekleniyor');
            $table->dateTime('son_hatirlatma_tarihi')->nullable();
            $table->dateTime('sonraki_hak_tarihi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. HATIRLATMA YORUMLARI (TARTIŞMA ALANI)
        Schema::create('sikayet_hatirlatma_yorumlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sikayet_hatirlatma_id')->constrained('sikayet_hatirlatmalari')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('yorum');
            $table->timestamps();
        });

        // 3. HATIRLATMA BİLDİRİLENLER (KİMLERE GİTTİĞİ)
        Schema::create('sikayet_hatirlatma_bildirilenler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sikayet_hatirlatma_id')->constrained('sikayet_hatirlatmalari')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->string('bildirim_rolu'); // 'Direktör', 'Kalite Yöneticisi' vb.
            $table->timestamps();
        });

        // 4. OTOMATİK HATIRLATICI KURALLARI
        Schema::create('sikayet_hatirlatici_kurallari', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->boolean('aktif')->default(true);
            $table->json('proje_durumlari'); // ["Yeni", "Atandı", ...]
            $table->enum('siklik', ['gunluk', 'haftalik', 'aylik']);
            $table->json('haftanin_gunleri')->nullable(); // [1, 2, 3] (1: Pazartesi)
            $table->time('saat');
            $table->json('bildirim_rolleri'); // ["Direktör", ...]
            $table->boolean('sikayeti_girene_bildir')->default(false);
            $table->boolean('musteriye_bildir')->default(false);
            $table->json('ek_kullanici_ids')->nullable(); // [12, 15]
            $table->dateTime('son_calisma_tarihi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sikayet_hatirlatici_kurallari');
        Schema::dropIfExists('sikayet_hatirlatma_bildirilenler');
        Schema::dropIfExists('sikayet_hatirlatma_yorumlari');
        Schema::dropIfExists('sikayet_hatirlatmalari');
    }
};
