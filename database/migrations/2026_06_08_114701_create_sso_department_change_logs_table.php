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
        Schema::create('sso_department_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('old_bolum_id')->nullable();
            $table->foreignId('new_bolum_id')->constrained('bolumler')->cascadeOnDelete();
            $table->timestamps();

            // Sadece foreign referansını ekle, id olmayabilir
            $table->foreign('old_bolum_id')->references('id')->on('bolumler')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sso_department_change_logs');
    }
};
