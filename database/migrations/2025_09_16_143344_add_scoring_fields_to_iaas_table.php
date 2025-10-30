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
            $table->tinyInteger('risk')->nullable()->after('durum');
            $table->decimal('kazanc_miktar', 15, 2)->nullable()->after('risk');
            $table->string('kazanc_birim', 10)->nullable()->after('kazanc_miktar');
            $table->decimal('butce_miktar', 15, 2)->nullable()->after('kazanc_birim');
            $table->string('butce_birim', 10)->nullable()->after('butce_miktar');
            $table->decimal('puan', 8, 2)->nullable()->after('butce_birim');
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
