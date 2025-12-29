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
    Schema::table('customers', function (Blueprint $table) {
        // is_active sütunu YOKSA ekle
        if (!Schema::hasColumn('customers', 'is_active')) {
            $table->boolean('is_active')->default(true)->after('location_type');
        }
        
        // passive_reason sütunu YOKSA ekle
        if (!Schema::hasColumn('customers', 'passive_reason')) {
            $table->text('passive_reason')->nullable()->after('is_active');
        }
    });
}

public function down()
{
    Schema::table('customers', function (Blueprint $table) {
        // Geri alırken sütunları sil
        if (Schema::hasColumn('customers', 'is_active')) {
            $table->dropColumn('is_active');
        }
        if (Schema::hasColumn('customers', 'passive_reason')) {
            $table->dropColumn('passive_reason');
        }
    });
}
};
