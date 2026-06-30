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
        if (Schema::hasTable('saha_kalite_analisti_bolum')) {
            Schema::rename('saha_kalite_analisti_bolum', 'musteri_saha_temsilcisi_bolum');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_saha_temsilcisi_bolum', function (Blueprint $table) {
            $table->renameIndex('temsilci_bolum_unique', 'analist_bolum_unique');
        });
        
        Schema::rename('musteri_saha_temsilcisi_bolum', 'saha_kalite_analisti_bolum');
    }
};
