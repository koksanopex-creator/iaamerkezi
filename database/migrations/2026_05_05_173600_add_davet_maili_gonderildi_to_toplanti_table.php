<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->boolean('davet_maili_gonderildi')->default(false)->after('hatirlatma_gonderildi');
        });
    }

    public function down(): void
    {
        Schema::table('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->dropColumn('davet_maili_gonderildi');
        });
    }
};
