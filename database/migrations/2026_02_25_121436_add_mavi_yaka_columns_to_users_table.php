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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_mavi_yaka')->default(false)->after('is_personnel');
            $table->string('tc_kimlik_no', 11)->nullable()->unique()->after('is_mavi_yaka');
            $table->string('sicil_no')->nullable()->after('tc_kimlik_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_mavi_yaka', 'tc_kimlik_no', 'sicil_no']);
        });
    }
};
