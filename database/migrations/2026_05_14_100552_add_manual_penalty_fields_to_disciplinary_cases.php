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
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->string('manual_penalty_name')->nullable()->after('final_karar');
            $table->unsignedBigInteger('manual_penalty_by')->nullable()->after('manual_penalty_name');
            
            $table->foreign('manual_penalty_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropForeign(['manual_penalty_by']);
            $table->dropColumn(['manual_penalty_name', 'manual_penalty_by']);
        });
    }
};
