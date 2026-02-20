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
            
            // 1. Talep İsteyen Kişi (talep_gerekcesi'nden sonra ekle)
            if (!Schema::hasColumn('iaas', 'talep_isteyen_user_id')) {
                $table->unsignedBigInteger('talep_isteyen_user_id')
                      ->nullable()
                      ->after('talep_gerekcesi');
            }

            // 2. Kapanış Notu (talep_admin_notu'ndan sonra ekle)
            // Not: Senin sistemde 'talep_red_gerekcesi' yerine 'talep_admin_notu' olduğunu teyit etmiştik.
            if (!Schema::hasColumn('iaas', 'kapanis_notu')) {
                $table->text('kapanis_notu')
                      ->nullable()
                      ->after('talep_admin_notu'); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iaas', function (Blueprint $table) {
            $table->dropColumn(['talep_isteyen_user_id', 'kapanis_notu']);
        });
    }
};