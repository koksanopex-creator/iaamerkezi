<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('iaa_user', function (Blueprint $table) {
        // Varsayılan 'onaylandi' yapıyoruz ki mevcut kayıtlar bozulmasın
        $table->string('durum')->default('onaylandi')->after('rol'); 
    });
}

public function down()
{
    Schema::table('iaa_user', function (Blueprint $table) {
        $table->dropColumn('durum');
    });
}
};
