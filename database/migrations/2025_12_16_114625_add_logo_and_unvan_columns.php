<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Müşterilere LOGO ekliyoruz
        Schema::table('customers', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('name');
        });

        // 2. Kullanıcılara (Yetkililere) ÜNVAN ekliyoruz
        Schema::table('users', function (Blueprint $table) {
            $table->string('unvan')->nullable()->after('name'); // Örn: Satın Alma Müdürü
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('logo_path');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unvan');
        });
    }
};