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
            $table->unsignedBigInteger('sikayet_workflow_id')->nullable()->after('bolum_kategori_id');
            $table->foreign('sikayet_workflow_id')->references('id')->on('iaa_workflows')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bolumler', function (Blueprint $table) {
            $table->dropForeign(['sikayet_workflow_id']);
            $table->dropColumn('sikayet_workflow_id');
        });
    }
};
