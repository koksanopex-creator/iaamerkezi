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
    Schema::table('iaas', function (Blueprint $table) {
        // 'durum' sütunundan sonra 'talep_gerekcesi' ekle
        $table->text('talep_gerekcesi')->nullable()->after('durum');
    });
}

public function down()
{
    Schema::table('iaas', function (Blueprint $table) {
        $table->dropColumn('talep_gerekcesi');
    });
}
};
