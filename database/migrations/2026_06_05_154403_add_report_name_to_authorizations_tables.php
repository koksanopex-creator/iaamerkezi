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
        Schema::table('report_role_authorizations', function (Blueprint $table) {
            if (!Schema::hasColumn('report_role_authorizations', 'report_name')) {
                $table->enum('report_name', ['analiz_raporu', 'karsilastirma_raporu'])->default('analiz_raporu')->after('id');
                $table->dropUnique('report_role_authorizations_role_name_unique');
                $table->unique(['role_name', 'report_name']);
            }
        });

        Schema::table('report_user_authorizations', function (Blueprint $table) {
            if (!Schema::hasColumn('report_user_authorizations', 'report_name')) {
                $table->dropForeign(['user_id']);
                $table->enum('report_name', ['analiz_raporu', 'karsilastirma_raporu'])->default('analiz_raporu')->after('id');
                $table->dropUnique('report_user_authorizations_user_id_unique');
                $table->unique(['user_id', 'report_name']);
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('report_user_authorizations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'report_name']);
            $table->dropColumn('report_name');
            $table->unique('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('report_role_authorizations', function (Blueprint $table) {
            $table->dropUnique(['role_name', 'report_name']);
            $table->dropColumn('report_name');
            $table->unique('role_name');
        });
    }
};
