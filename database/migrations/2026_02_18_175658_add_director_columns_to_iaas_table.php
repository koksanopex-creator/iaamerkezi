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
        Schema::table('iaas', function (Blueprint $table) {
            $table->text('direktor_notu')->nullable()->after('yonetici_notu');
            $table->timestamp('direktor_onay_tarihi')->nullable()->after('onaylanma_tarihi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropColumn(['direktor_notu', 'direktor_onay_tarihi']);
        });
    }
};
