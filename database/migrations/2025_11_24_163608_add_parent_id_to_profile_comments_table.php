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
        Schema::table('profile_comments', function (Blueprint $table) {
            // Bir yorumun üst yorumu olabilir (Cevap ise). Yoksa ana yorumdur.
            // Yorum silinirse cevapları da silinsin (cascade).
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('profile_comments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_comments', function (Blueprint $table) {
            //
        });
    }
};
