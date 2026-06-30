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
        Schema::table('bolumler', function (Blueprint $table) {
            $table->string('category')->nullable()->after('ad'); // Üretim, Teknik, Depo vb.
            $table->boolean('has_machines')->default(false)->after('category'); // Makine yönetimi var mı?
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bolumler', function (Blueprint $table) {
            $table->dropColumn(['category', 'has_machines']);
        });
    }
};
