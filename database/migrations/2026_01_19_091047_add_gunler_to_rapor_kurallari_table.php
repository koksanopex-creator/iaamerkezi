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
        Schema::table('rapor_kurallari', function (Blueprint $table) {
            // alicilar sütunundan sonra ekleyelim
            $table->json('gunler')->nullable()->after('gonderim_saati'); 
        });
    }

    public function down()
    {
        Schema::table('rapor_kurallari', function (Blueprint $table) {
            $table->dropColumn('gunler');
        });
    }
};
