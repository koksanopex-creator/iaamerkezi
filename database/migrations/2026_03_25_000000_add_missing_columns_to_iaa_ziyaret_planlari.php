<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            if (!Schema::hasColumn('iaa_ziyaret_planlari', 'planner_id')) {
                $table->foreignId('planner_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('iaa_ziyaret_planlari', 'rejection_reason_director')) {
                $table->text('rejection_reason_director')->nullable();
            }
            if (!Schema::hasColumn('iaa_ziyaret_planlari', 'rejection_reason_quality')) {
                $table->text('rejection_reason_quality')->nullable();
            }
            if (!Schema::hasColumn('iaa_ziyaret_planlari', 'rejection_reason_superadmin')) {
                $table->text('rejection_reason_superadmin')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->dropColumn([
                'planner_id',
                'rejection_reason_director',
                'rejection_reason_quality',
                'rejection_reason_superadmin'
            ]);
        });
    }
};
