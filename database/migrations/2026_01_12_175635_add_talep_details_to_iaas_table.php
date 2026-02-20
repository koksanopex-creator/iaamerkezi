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
        $table->string('talep_dosyasi')->nullable()->after('talep_gerekcesi'); // Dosya Yolu
        $table->text('talep_kalite_notu')->nullable()->after('talep_dosyasi'); // Serkan'ın Notu
        $table->text('talep_admin_notu')->nullable()->after('talep_kalite_notu'); // Superadmin'in Notu
    });
}

public function down()
{
    Schema::table('iaas', function (Blueprint $table) {
        $table->dropColumn(['talep_dosyasi', 'talep_kalite_notu', 'talep_admin_notu']);
    });
}
};
