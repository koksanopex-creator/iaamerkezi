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
        Schema::table('sikayet_kategorileri', function (Blueprint $table) {
            $table->foreignId('bolum_id')->nullable()->constrained('bolumler')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sikayet_kategorileri', function (Blueprint $table) {
            $table->dropForeign(['bolum_id']);
            $table->dropColumn('bolum_id');
        });
    }
};
