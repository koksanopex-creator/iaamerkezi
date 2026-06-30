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
        Schema::create('musteri_sikayeti_yonetici_rapor_kurallari', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->boolean('aktif')->default(true);
            $table->enum('siklik', ['gunluk', 'haftalik', 'aylik'])->default('haftalik');
            $table->json('haftanin_gunleri')->nullable(); // Haftalık ise hangi günler?
            $table->time('saat');
            $table->boolean('mail_aktif_et')->default(true);
            $table->boolean('zili_aktif_et')->default(true);
            $table->string('mail_konusu')->nullable();
            $table->text('mail_taslagi')->nullable();
            $table->string('bildirim_metni')->nullable();
            $table->timestamp('son_calisma_tarihi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musteri_sikayeti_yonetici_rapor_kurallari');
    }
};
