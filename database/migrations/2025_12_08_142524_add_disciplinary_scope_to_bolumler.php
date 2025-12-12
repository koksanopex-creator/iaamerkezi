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
            // Hangi bölümlere tutanak tutabilir? (Boşsa sadece kendi bölümü, '*' ise hepsi)
            $table->json('disciplinary_target_depts')->nullable()->after('is_disciplinary_global'); 
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
