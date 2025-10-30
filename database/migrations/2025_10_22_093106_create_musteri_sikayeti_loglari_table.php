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
        Schema::create('musteri_sikayeti_loglari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musteri_sikayeti_id')->constrained('musteri_sikayetleri')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // İşlemi yapan kullanıcı
            $table->string('eylem'); // Örn: "Takım Atandı", "Ek Süre Talep Edildi"
            $table->text('aciklama')->nullable(); // Örn: "Super Admin, 'Kapak Çözüm Takımı'nı atadı."
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musteri_sikayeti_loglari');
    }
};