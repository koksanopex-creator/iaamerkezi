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
            $table->unsignedBigInteger('atamadaki_lider_id')->nullable()->after('atanan_takim_id');
            $table->foreign('atamadaki_lider_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['atamadaki_lider_id']);
            }
            $table->dropColumn('atamadaki_lider_id');
        });
    }
};
