<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bolumler', function (Blueprint $table) {
            // Eski 'category' sütununu kaldır (varsa)
            if (Schema::hasColumn('bolumler', 'category')) {
                $table->dropColumn('category');
            }

            // Yeni sütunları ekle
            $table->foreignId('bolum_kategori_id')->nullable()->constrained('bolum_kategorileri')->nullOnDelete();
            $table->string('logo_yolu')->nullable(); // Bölüm Logosu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bolumler', function (Blueprint $table) {
            $table->string('category')->nullable(); // Geri alırken ekle
            $table->dropForeign(['bolum_kategori_id']);
            $table->dropColumn('bolum_kategori_id');
            $table->dropColumn('logo_yolu');
        });
    }
};
