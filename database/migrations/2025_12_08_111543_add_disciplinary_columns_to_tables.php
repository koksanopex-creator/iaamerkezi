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
        // 1. Bölümlere: "Bu bölüm fabrikanın tamamına tutanak tutabilir mi?" (Örn: İSG, Güvenlik)
        if (!Schema::hasColumn('bolumler', 'is_disciplinary_global')) {
            Schema::table('bolumler', function (Blueprint $table) {
                $table->boolean('is_disciplinary_global')->default(false)->after('ad'); 
            });
        }

        // 2. Kullanıcılara: "Bu kişi tutanak tutma yetkisine sahip mi?" (Lider tarafından verilir)
        if (!Schema::hasColumn('users', 'can_issue_disciplinary')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('can_issue_disciplinary')->default(false)->after('bolum_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
