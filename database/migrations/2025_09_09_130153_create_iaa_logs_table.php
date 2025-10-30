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
        // İAA GEÇMİŞ KAYITLARI (LOG) TABLOSU
        // Bir İAA üzerinde yapılan her işlemi (onay, ret, atama vb.) kaydeder.
        // =================================================================
        Schema::create('iaa_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iaa_id')->constrained('iaas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // İşlemi yapan kullanıcı
            $table->string('eylem'); // 'Oluşturuldu', 'Onaylandı', 'Reddedildi', 'Atandı'
            $table->text('aciklama')->nullable(); // İşlemle ilgili ek not
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_logs');
    }
};