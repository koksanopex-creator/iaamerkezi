<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->text('talep_direktor_notu')->nullable();
            $table->unsignedBigInteger('talep_direktor_user_id')->nullable();
            $table->timestamp('talep_direktor_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropColumn(['talep_direktor_notu', 'talep_direktor_user_id', 'talep_direktor_at']);
        });
    }
};
