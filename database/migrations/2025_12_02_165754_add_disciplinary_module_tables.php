<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. DİSİPLİN KATEGORİLERİ (İş Güvenliği, Etik vb.)
        if (!Schema::hasTable('disciplinary_categories')) {
            Schema::create('disciplinary_categories', function (Blueprint $table) {
                $table->id();
                $table->string('ad');
                $table->timestamps();
            });
        }

        // 2. ETKİ / ŞİDDET (Kişiye Karşı - 5 Puan)
        if (!Schema::hasTable('disciplinary_impacts')) {
            Schema::create('disciplinary_impacts', function (Blueprint $table) {
                $table->id();
                $table->string('tanim');
                $table->integer('puan'); 
                $table->timestamps();
            });
        }

        // 3. KAPSAM (Sistemi Bozar - 3 Puan)
        if (!Schema::hasTable('disciplinary_scopes')) {
            Schema::create('disciplinary_scopes', function (Blueprint $table) {
                $table->id();
                $table->string('tanim');
                $table->integer('puan');
                $table->timestamps();
            });
        }

        // 4. SUÇ TANIMLARI (DAVRANIŞLAR)
        if (!Schema::hasTable('disciplinary_behaviors')) {
            Schema::create('disciplinary_behaviors', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id');
                $table->text('tanim');
                $table->string('yasal_dayanak')->nullable();
                $table->boolean('aktif_mi')->default(true);
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('disciplinary_categories')->onDelete('cascade');
            });
        }

        // 5. TEKRAR KATSAYILARI
        if (!Schema::hasTable('disciplinary_multipliers')) {
            Schema::create('disciplinary_multipliers', function (Blueprint $table) {
                $table->id();
                $table->integer('tekrar_sayisi');
                $table->decimal('katsayi', 5, 2);
                $table->timestamps();
            });
        }

        // 6. CEZA SKALASI
        if (!Schema::hasTable('disciplinary_penalty_scales')) {
            Schema::create('disciplinary_penalty_scales', function (Blueprint $table) {
                $table->id();
                $table->integer('min_puan');
                $table->integer('max_puan');
                $table->string('ceza_adi');
                $table->timestamps();
            });
        }

        // 7. DİSİPLİN DOSYALARI (CASES)
        if (!Schema::hasTable('disciplinary_cases')) {
            Schema::create('disciplinary_cases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');      // Suçu işleyen
                $table->unsignedBigInteger('reporter_id');  // Bildiren
                $table->unsignedBigInteger('behavior_id');  // Suç Tanımı
                
                // Seçilen Etki ve Kapsam
                $table->unsignedBigInteger('impact_id')->nullable();
                $table->unsignedBigInteger('scope_id')->nullable();

                $table->enum('durum', ['Taslak', 'Savunma Bekleniyor', 'Kurulda', 'Karar Verildi', 'İptal'])->default('Taslak');
                $table->dateTime('olay_tarihi');
                $table->text('olay_aciklamasi');
                $table->json('kanit_dosyalari')->nullable();

                // Savunma
                $table->text('savunma_metni')->nullable();
                $table->dateTime('savunma_tarihi')->nullable();

                // Hesaplama
                $table->integer('tekrar_sayisi')->default(1);
                $table->integer('hesaplanan_puan')->default(0);
                $table->string('final_karar')->nullable();

                $table->timestamps();
                $table->softDeletes();
                
                // İlişkiler
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('behavior_id')->references('id')->on('disciplinary_behaviors')->onDelete('cascade');
           
                // === EKLENECEK KISIM (ETKİ VE KAPSAM İLİŞKİLERİ) ===
                $table->foreign('impact_id')->references('id')->on('disciplinary_impacts')->onDelete('set null');
                $table->foreign('scope_id')->references('id')->on('disciplinary_scopes')->onDelete('set null');
            });
        }

        // 8. KURUL OYLARI
        if (!Schema::hasTable('disciplinary_votes')) {
            Schema::create('disciplinary_votes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('case_id');
                $table->unsignedBigInteger('user_id'); // Oyu veren
                $table->enum('oy_yonu', ['Ceza Verilsin', 'Ceza Verilmesin', 'Ek Soruşturma', 'Çekimser']);
                $table->text('yorum')->nullable();
                $table->timestamps();

                $table->foreign('case_id')->references('id')->on('disciplinary_cases')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // Sadece yeni tabloları siler, eskilere dokunmaz
        Schema::dropIfExists('disciplinary_votes');
        Schema::dropIfExists('disciplinary_cases');
        Schema::dropIfExists('disciplinary_penalty_scales');
        Schema::dropIfExists('disciplinary_multipliers');
        Schema::dropIfExists('disciplinary_behaviors');
        Schema::dropIfExists('disciplinary_scopes');
        Schema::dropIfExists('disciplinary_impacts');
        Schema::dropIfExists('disciplinary_categories');
    }
};