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
        Schema::table('iaa_talepleri', function (Blueprint $table) {
            $table->json('workflow_snapshot')->nullable()->after('iaa_workflow_id')->comment('Atama anındaki iş akışı adımlarının kopyası');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_talepleri', function (Blueprint $table) {
            $table->dropColumn('workflow_snapshot');
        });
    }
};
