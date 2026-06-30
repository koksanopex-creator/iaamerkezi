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
            $table->text('icerik')->nullable()->after('yer')->comment('Toplantı içeriği / gündem maddeleri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->dropColumn('icerik');
        });
    }
};
