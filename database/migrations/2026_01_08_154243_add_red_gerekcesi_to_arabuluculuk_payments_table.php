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
        Schema::table('arabuluculuk_payments', function (Blueprint $table) {
            // 'dekont_path' yerine 'odeme_durumu'ndan sonraya ekliyoruz.
            // Eğer o da yoksa ->after() kısmını tamamen sil, en sona eklesin.
            $table->text('red_gerekcesi')->nullable()->after('odeme_durumu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arabuluculuk_payments', function (Blueprint $table) {
            // Eğer migration'ı geri alırsak bu sütunu sil.
            $table->dropColumn('red_gerekcesi');
        });
    }
};