<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iaa_workflow_steps', function (Blueprint $table) {
            // Yeni JSON sütununu ekle (örneğin default_duration_days'den sonra)
            $table->json('widgets')->nullable()->after('default_duration_days');
        });
    }

    public function down(): void
    {
        Schema::table('iaa_workflow_steps', function (Blueprint $table) {
            $table->dropColumn('widgets');
        });
    }
};