<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->enum('durum', ['Taslak', 'Savunma Bekleniyor', 'Yönetici Değerlendirmesi', 'Kurulda', 'Karar Verildi', 'İptal', 'İtiraz Edildi'])
                ->default('Taslak')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->enum('durum', ['Taslak', 'Savunma Bekleniyor', 'Yönetici Değerlendirmesi', 'Kurulda', 'Karar Verildi', 'İptal'])
                ->default('Taslak')
                ->change();
        });
    }
};
