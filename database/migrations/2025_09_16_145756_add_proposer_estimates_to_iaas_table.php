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
        Schema::table('iaas', function (Blueprint $table) {
            $table->decimal('oneren_kazanc_miktar', 15, 2)->nullable()->after('puan');
            $table->string('oneren_kazanc_birim', 10)->nullable()->after('oneren_kazanc_miktar');
            $table->decimal('oneren_butce_miktar', 15, 2)->nullable()->after('oneren_kazanc_birim');
            $table->string('oneren_butce_birim', 10)->nullable()->after('oneren_butce_miktar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            //
        });
    }
};
