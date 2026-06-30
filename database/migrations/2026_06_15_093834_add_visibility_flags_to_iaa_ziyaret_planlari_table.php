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
            $table->boolean('is_visit_notes_visible_to_customer')->default(false)->after('visit_notes');
            $table->boolean('is_visit_file_visible_to_customer')->default(false)->after('visit_file');
            $table->boolean('is_findings_visible_to_customer')->default(false)->after('findings');
            $table->boolean('is_result_visible_to_customer')->default(false)->after('result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->dropColumn([
                'is_visit_notes_visible_to_customer',
                'is_visit_file_visible_to_customer',
                'is_findings_visible_to_customer',
                'is_result_visible_to_customer'
            ]);
        });
    }
};
