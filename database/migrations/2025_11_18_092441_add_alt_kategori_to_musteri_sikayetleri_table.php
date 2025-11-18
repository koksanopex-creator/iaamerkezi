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
            // Seçilen alt kategoriye (örn: "Leke") bağlanır
            $table->foreignId('sikayet_alt_kategori_id')->nullable()->constrained('sikayet_alt_kategorileri')->nullOnDelete()->after('sikayet_kategorisi_id');
            // "Diğer" seçeneği için girilen metin
            $table->text('sikayet_alt_kategori_diger')->nullable()->after('sikayet_alt_kategori_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            //
        });
    }
};
