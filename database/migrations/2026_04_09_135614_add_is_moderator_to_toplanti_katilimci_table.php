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
        Schema::table('disiplin_kurulu_toplanti_katilimci', function (Blueprint $table) {
            $table->boolean('is_moderator')->default(false)->after('katilim_durumu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disiplin_kurulu_toplanti_katilimci', function (Blueprint $table) {
            $table->dropColumn('is_moderator');
        });
    }
};
