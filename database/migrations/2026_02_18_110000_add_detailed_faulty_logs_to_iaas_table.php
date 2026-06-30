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
            // Superadmin Notu
            $table->text('hatali_bildirim_superadmin_notu')->nullable()->after('hatali_bildirim_direktor_notu');

            // Tarih Damgaları
            $table->timestamp('hatali_bildirim_kalite_at')->nullable()->after('hatali_bildirim_kalite_notu');
            $table->timestamp('hatali_bildirim_direktor_at')->nullable()->after('hatali_bildirim_direktor_notu');
            $table->timestamp('hatali_bildirim_superadmin_at')->nullable()->after('hatali_bildirim_superadmin_notu');

            // Onaylayan Kullanıcılar
            $table->foreignId('hatali_bildirim_kalite_user_id')->nullable()->constrained('users')->after('hatali_bildirim_kalite_at');
            $table->foreignId('hatali_bildirim_direktor_user_id')->nullable()->constrained('users')->after('hatali_bildirim_direktor_at');
            $table->foreignId('hatali_bildirim_superadmin_user_id')->nullable()->constrained('users')->after('hatali_bildirim_superadmin_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropColumn([
                'hatali_bildirim_superadmin_notu',
                'hatali_bildirim_kalite_at',
                'hatali_bildirim_direktor_at',
                'hatali_bildirim_superadmin_at',
                'hatali_bildirim_kalite_user_id',
                'hatali_bildirim_direktor_user_id',
                'hatali_bildirim_superadmin_user_id'
            ]);
        });
    }
};
