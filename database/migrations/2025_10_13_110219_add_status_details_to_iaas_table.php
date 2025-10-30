<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
        Schema::table('iaas', function (Blueprint $table) {
            // Yönetici tarafından girilen not
            $table->text('son_durum_notu')->nullable()->after('durum');
            // Durumun ne zaman değiştiği
            $table->timestamp('durum_degistirme_tarihi')->nullable()->after('son_durum_notu');
        });
    }

    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropColumn(['son_durum_notu', 'durum_degistirme_tarihi']);
        });
    }
};