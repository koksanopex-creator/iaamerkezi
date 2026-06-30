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
        Schema::create('report_role_authorizations', function (Blueprint $table) {
            $table->id();
            $table->string('role_name')->unique()->comment('Spatie rol ismi');
            $table->enum('data_scope', [
                'all',                    // Tüm veriler
                'own_department',         // Kendi bölümü
                'responsible_departments', // Sorumlu olduğu bölümler (Direktör vb.)
                'specific_departments',   // Belirli seçili bölümler
            ])->default('own_department')->comment('Veri erişim kapsamı');
            $table->json('specific_department_ids')->nullable()->comment('Belirli bölüm ID listesi (JSON)');
            $table->timestamps();
        });

        // Varsayılan kayıtlar: Superadmin ve Yonetim tam erişim
        \Illuminate\Support\Facades\DB::table('report_role_authorizations')->insert([
            [
                'role_name' => 'Superadmin',
                'data_scope' => 'all',
                'specific_department_ids' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'Yonetim',
                'data_scope' => 'all',
                'specific_department_ids' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_role_authorizations');
    }
};
