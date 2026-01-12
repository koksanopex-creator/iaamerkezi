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
        Schema::table('arabuluculuk_anlasma_maddesis', function (Blueprint $table) {
            $table->string('hukuki_dayanak')->nullable()->after('icerik'); // Örn: İş Kanunu Madde 25/II
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arabuluculuk_anlasma_maddesis', function (Blueprint $table) {
            //
        });
    }
};
