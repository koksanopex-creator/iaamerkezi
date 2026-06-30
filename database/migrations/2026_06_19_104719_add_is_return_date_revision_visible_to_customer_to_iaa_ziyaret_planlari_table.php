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
            if (!Schema::hasColumn('iaa_ziyaret_planlari', 'is_return_date_revision_visible_to_customer')) {
                $table->boolean('is_return_date_revision_visible_to_customer')->default(false)->after('return_date_revision_response');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            if (Schema::hasColumn('iaa_ziyaret_planlari', 'is_return_date_revision_visible_to_customer')) {
                $table->dropColumn('is_return_date_revision_visible_to_customer');
            }
        });
    }
};
