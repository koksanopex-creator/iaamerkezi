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
            $table->json('additional_products')->nullable()->after('customer_product_id');
            $table->json('visit_reasons')->nullable()->after('visit_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->dropColumn('additional_products');
            $table->dropColumn('visit_reasons');
        });
    }
};
