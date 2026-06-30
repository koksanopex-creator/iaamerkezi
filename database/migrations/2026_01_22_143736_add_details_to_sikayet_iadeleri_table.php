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
            $table->date('iade_tarihi')->nullable()->after('user_id'); // İadenin fiili tarihi
            $table->decimal('toplam_parti_miktari', 15, 2)->nullable()->after('miktar'); // Parti büyüklüğü (Örn: 20 Ton)
        });
    }

    public function down()
    {
        Schema::table('sikayet_iadeleri', function (Blueprint $table) {
            $table->dropColumn(['iade_tarihi', 'toplam_parti_miktari']);
        });
    }
};
