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
        Schema::table('bolumler', function (Blueprint $table) {
            // Eğer sütun yoksa ekle
            if (!Schema::hasColumn('bolumler', 'is_disciplinary_global')) {
                $table->boolean('is_disciplinary_global')->default(false)->after('ad');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bolumler', function (Blueprint $table) {
            //
        });
    }
};
