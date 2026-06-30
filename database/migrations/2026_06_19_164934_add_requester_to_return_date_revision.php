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
            $table->unsignedBigInteger('return_date_revision_requested_by')->nullable()->after('return_date_revision_requested_date');
            
            $table->foreign('return_date_revision_requested_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->dropForeign(['return_date_revision_requested_by']);
            $table->dropColumn('return_date_revision_requested_by');
        });
    }
};
