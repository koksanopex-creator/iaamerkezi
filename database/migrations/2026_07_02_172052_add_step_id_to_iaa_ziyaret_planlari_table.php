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
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->foreignId('iaa_workflow_step_id')->nullable()->constrained('iaa_workflow_steps')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->dropForeign(['iaa_workflow_step_id']);
            $table->dropColumn('iaa_workflow_step_id');
        });
    }
};
