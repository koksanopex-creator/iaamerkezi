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
        Schema::table('iaas', function (Blueprint $沟通) {
            $沟通->text('hatali_bildirim_kalite_notu')->nullable()->after('hatali_bildirim_gerekcesi');
            $沟通->text('hatali_bildirim_direktor_notu')->nullable()->after('hatali_bildirim_kalite_notu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $沟通) {
            $沟通->dropColumn(['hatali_bildirim_kalite_notu', 'hatali_bildirim_direktor_notu']);
        });
    }
};
