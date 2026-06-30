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
        Schema::table('sikayet_iadeleri', function (Blueprint $table) {
            $table->boolean('musteri_gorebilir_mi')->default(0)->after('aciklama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sikayet_iadeleri', function (Blueprint $table) {
            $table->dropColumn('musteri_gorebilir_mi');
        });
    }
};
