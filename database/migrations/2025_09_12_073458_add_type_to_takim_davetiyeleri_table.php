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
        Schema::table('takim_davetiyeleri', function (Blueprint $table) {
            // 'davet' -> Lider tarafından gönderildi.
            // 'istek' -> Kullanıcı tarafından gönderildi.
            $table->enum('type', ['davet', 'istek'])->default('davet')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('takim_davetiyeleri', function (Blueprint $table) {
            //
        });
    }
};
