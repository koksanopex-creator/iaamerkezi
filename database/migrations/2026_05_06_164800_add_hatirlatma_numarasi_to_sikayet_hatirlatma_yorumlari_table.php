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
        Schema::table('sikayet_hatirlatma_yorumlari', function (Blueprint $table) {
            $table->integer('hatirlatma_numarasi')->default(1)->after('sikayet_hatirlatma_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sikayet_hatirlatma_yorumlari', function (Blueprint $table) {
            $table->dropColumn('hatirlatma_numarasi');
        });
    }
};
