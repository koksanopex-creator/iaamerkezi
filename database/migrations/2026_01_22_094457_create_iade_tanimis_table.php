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
        Schema::create('iade_tanimlari', function (Blueprint $table) {
            $table->id();
            // Hangi bölüme ait? (Preform, Kapak vs.)
            $table->foreignId('bolum_id')->constrained('bolumler')->onDelete('cascade');
            
            // Tanım Tipi: 'urun_grubu', 'iade_sebebi', 'birim'
            $table->string('tip'); 
            
            // Değer: '35gr T.off', 'Ovallik', 'Ton', 'Adet'
            $table->string('deger'); 
            
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iade_tanimis');
    }
};
