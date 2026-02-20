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
            $table->text('hatali_bildirim_gerekcesi')->nullable()->after('durum');
            $table->timestamp('hatali_bildirim_tarihi')->nullable()->after('hatali_bildirim_gerekcesi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropColumn(['hatali_bildirim_gerekcesi', 'hatali_bildirim_tarihi']);
        });
    }
};
