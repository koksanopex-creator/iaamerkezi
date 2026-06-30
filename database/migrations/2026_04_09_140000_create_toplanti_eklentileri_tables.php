<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Toplantılar Tablosuna Eklemeler
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->dateTime('baslatilma_at')->nullable()->after('baslangic_tarihi');
            $table->dateTime('bitirilme_at')->nullable()->after('baslatilma_at');
            $table->integer('planlanan_sure_dk')->default(60)->after('bitirilme_at');
            $table->text('erteleme_sebebi')->nullable()->after('planlanan_sure_dk');
            $table->text('iptal_sebebi')->nullable()->after('erteleme_sebebi');
            $table->integer('hatirlatma_dk')->nullable()->after('iptal_sebebi'); // X dk önce bildirim
            $table->boolean('hatirlatma_gonderildi')->default(false)->after('hatirlatma_dk');
        });

        // 2. Toplantı Aksiyonları (Görevler)
        Schema::create('toplanti_aksiyonlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toplanti_id')->constrained('disiplin_kurulu_toplanti')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Aksiyon atanan kişi
            $table->text('icerik');
            $table->enum('durum', ['beklemede', 'tamamlandi'])->default('beklemede');
            $table->timestamps();
        });

        // 3. Toplantı Oylamaları
        Schema::create('toplanti_oylamalari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toplanti_id')->constrained('disiplin_kurulu_toplanti')->onDelete('cascade');
            $table->foreignId('baslatan_id')->constrained('users');
            $table->string('konu');
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 4. Oylama Sonuçları (Bireysel Oylar)
        Schema::create('toplanti_oylari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('oylama_id')->constrained('toplanti_oylamalari')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->enum('oy', ['lehte', 'aleyhte', 'cekimser']);
            $table->timestamps();
            $table->unique(['oylama_id', 'user_id']);
        });

        // 5. Katılımcı Gizli Notları
        Schema::create('toplanti_gizli_notlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toplanti_id')->constrained('disiplin_kurulu_toplanti')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->longText('not_icerigi');
            $table->timestamps();
            $table->unique(['toplanti_id', 'user_id']);
        });

        // 6. Beyin Fırtınası Panosu (Shared Board)
        Schema::create('toplanti_pano', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toplanti_id')->constrained('disiplin_kurulu_toplanti')->onDelete('cascade');
            $table->longText('icerik')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toplanti_pano');
        Schema::dropIfExists('toplanti_gizli_notlar');
        Schema::dropIfExists('toplanti_oylari');
        Schema::dropIfExists('toplanti_oylamalari');
        Schema::dropIfExists('toplanti_aksiyonlari');
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->dropColumn([
                'baslatilma_at', 'bitirilme_at', 'planlanan_sure_dk', 
                'erteleme_sebebi', 'iptal_sebebi', 'hatirlatma_dk', 'hatirlatma_gonderildi'
            ]);
        });
    }
};
