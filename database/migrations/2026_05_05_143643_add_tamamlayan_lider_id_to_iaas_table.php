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
        Schema::table('iaas', function (Blueprint $table) {
            $table->unsignedBigInteger('tamamlayan_lider_id')->nullable()->after('atanan_takim_id');
            
            $table->foreign('tamamlayan_lider_id')->references('id')->on('users')->onDelete('set null');
        });

        // Mevcut verileri senkronize et: Tamamlanmış projelerin o anki (mevcut) liderlerini bu sütuna kopyala
        DB::statement("UPDATE iaas SET tamamlayan_lider_id = (SELECT lider_user_id FROM takimlar WHERE takimlar.id = iaas.atanan_takim_id) WHERE durum = 'Tamamlandı' AND atanan_takim_id IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropForeign(['tamamlayan_lider_id']);
            $table->dropColumn('tamamlayan_lider_id');
        });
    }
};
