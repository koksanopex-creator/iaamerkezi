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
    Schema::table('arabuluculuk_cases', function (Blueprint $table) {
        // Anlaşma maddelerini (metin) saklamak için
        $table->text('anlasma_maddeleri')->nullable()->after('anlasilan_tutar');

        // Eğer karşı taraf teklifi sütunu da yoksa onu da ekleyelim (Migration'da görmemiştim)
        // Eğer varsa bu satırı sil.
        if (!Schema::hasColumn('arabuluculuk_cases', 'karsi_taraf_teklif')) {
            $table->decimal('karsi_taraf_teklif', 12, 2)->nullable()->after('talep_tutari');
        }
    });
}

public function down(): void
{
    Schema::table('arabuluculuk_cases', function (Blueprint $table) {
        $table->dropColumn(['anlasma_maddeleri', 'karsi_taraf_teklif']);
    });
}
};
