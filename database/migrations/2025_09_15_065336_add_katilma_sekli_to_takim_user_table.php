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
        Schema::table('takim_user', function (Blueprint $table) {
            // Lider -> Takımı kuran kişi
            // Davet -> Liderin davetini kabul etti
            // İstek -> Kullanıcının isteği lider tarafından kabul edildi
            $table->string('katilma_sekli')->default('Bilinmiyor')->after('user_id');
        });
    }
    
    public function down(): void
    {
        Schema::table('takim_user', function (Blueprint $table) {
            $table->dropColumn('katilma_sekli');
        });
    }
};
