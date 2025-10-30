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
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Yeni sütunu ekle (olusturan_kurul_uyesi_id'den sonra ekleyelim)
            $table->foreignId('sikayet_kategorisi_id')
                  ->nullable() // Başlangıçta boş olabilir veya eski kayıtlar için
                  ->after('olusturan_kurul_uyesi_id') // Bu sütundan sonra ekle
                  ->constrained('sikayet_kategorileri') // sikayet_kategorileri tablosuna bağla
                  ->onDelete('set null'); // Kategori silinirse bu alanı null yap
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // İlişkiyi ve sütunu kaldır
            $table->dropForeign(['sikayet_kategorisi_id']);
            $table->dropColumn('sikayet_kategorisi_id');
        });
    }
};