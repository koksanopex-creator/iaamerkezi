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
        Schema::table('sikayet_kategorileri', function (Blueprint $table) {
            // Bu kategori "Diğer" seçeneğini gösterecek mi?
            $table->boolean('diger_secenegi_goster')->default(false)->after('ad');
            // "Diğer" seçildiğinde gösterilecek açıklama (placeholder)
            $table->string('diger_aciklama_basligi')->nullable()->after('diger_secenegi_goster');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sikayet_kategorileri', function (Blueprint $table) {
            //
        });
    }
};
