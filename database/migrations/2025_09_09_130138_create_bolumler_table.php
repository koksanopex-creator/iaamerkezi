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
        // BÖLÜMLER TABLOSU
        // Uygulamadaki departmanları (Güvenlik, İSG vb.) tutar.
        // =================================================================
        Schema::create('bolumler', function (Blueprint $table) {
            $table->id();
            $table->string('ad', 150)->unique(); // Bölüm Adı
            $table->boolean('is_active')->default(true); // Aktif/Pasif durumu
            $table->timestamps();
            $table->softDeletes(); // Bölüm silindiğinde verileri korumak için
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bolumler');
    }
};