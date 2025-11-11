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
        // Proje/Şikayet adımları için yorumlar, loglar ve dosyalar
        Schema::create('proje_yorumlari', function (Blueprint $table) {
            $table->id();
            
            // Hangi Projeye ait?
            $table->foreignId('iaa_id')->constrained('iaas')->onDelete('cascade');
            
            // Hangi Adıma ait?
            $table->foreignId('iaa_workflow_step_id')->constrained('iaa_workflow_steps')->onDelete('cascade');
            
            // Yorumu kim yaptı? (Giriş yapmış bir kullanıcı ise)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Yorumu kim yaptı? (Misafir müşteri ise, bu ID'yi takip token'ı ile eşleştireceğiz)
            $table->foreignId('musteri_sikayeti_id')->nullable()->constrained('musteri_sikayetleri')->onDelete('set null');

            // Yorumu yapanın adını (User veya Misafir) buraya kopyala (Raporlama için kolaylık)
            $table->string('yapan_kisi_adi');

            // Yorum mu, yoksa otomatik Log mu?
            $table->enum('yorum_tipi', ['yorum', 'log', 'dosya'])->default('yorum');

            // Yorumun içeriği
            $table->text('yorum');

            // Eklenen dosya (İsteğe bağlı, 1 adet)
            $table->string('dosya_yolu')->nullable();
            $table->string('dosya_adi')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proje_yorumlari');
    }
};