<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('arabulucular', function (Blueprint $table) {
            // Kontrol 1: is_active sütunu YOKSA ekle
            if (!Schema::hasColumn('arabulucular', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('adres');
            }

            // Kontrol 2: created_by sütunu YOKSA ekle
            if (!Schema::hasColumn('arabulucular', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            }
        });
    }

    public function down()
    {
        Schema::table('arabulucular', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'created_by']);
        });
    }
};
