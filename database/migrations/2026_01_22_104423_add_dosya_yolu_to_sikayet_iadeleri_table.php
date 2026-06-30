<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sikayet_iadeleri', function (Blueprint $table) {
            $table->string('dosya_yolu')->nullable()->after('aciklama'); // Dosya yolu (Resim/PDF)
        });
    }

    public function down()
    {
        Schema::table('sikayet_iadeleri', function (Blueprint $table) {
            $table->dropColumn('dosya_yolu');
        });
    }
};
