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
            // Yeni sütunu ekle (örneğin musteri_iletisim'den sonra)
            $table->string('konum_tipi')->nullable()->after('musteri_iletisim'); // 'Yurt İçi' veya 'Yurt Dışı' gibi değerler için
        });
    }

    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->dropColumn('konum_tipi');
        });
    }
};
