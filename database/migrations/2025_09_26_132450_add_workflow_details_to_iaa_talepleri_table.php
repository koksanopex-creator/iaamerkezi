<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iaa_talepleri', function (Blueprint $table) {
            // YUKARIDAKİ id() SATIRINI SİLDİK. GERİSİ AYNI KALIYOR.
            $table->foreignId('iaa_workflow_id')->nullable()->constrained('iaa_workflows')->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('Talep Edildi');
        });
    }

    public function down(): void
    {
        Schema::table('iaa_talepleri', function (Blueprint $table) {
            $table->dropForeign(['iaa_workflow_id']);
            // 'id' kelimesini buradan da sildik.
            $table->dropColumn(['iaa_workflow_id', 'start_date', 'due_date', 'status']);
        });
    }
};