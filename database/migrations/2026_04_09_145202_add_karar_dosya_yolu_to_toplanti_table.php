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
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->string('karar_dosya_yolu', 500)->nullable()->after('toplanti_karari');
        });
    }

    public function down(): void
    {
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->dropColumn('karar_dosya_yolu');
        });
    }
};
