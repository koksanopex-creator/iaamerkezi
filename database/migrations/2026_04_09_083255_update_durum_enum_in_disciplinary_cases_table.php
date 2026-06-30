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
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->enum('durum', ['Taslak', 'Savunma Bekleniyor', 'Yönetici Değerlendirmesi', 'Kurulda', 'Karar Verildi', 'İptal'])
                ->default('Taslak')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->enum('durum', ['Taslak', 'Savunma Bekleniyor', 'Kurulda', 'Karar Verildi', 'İptal'])
                ->default('Taslak')
                ->change();
        });
    }
};
