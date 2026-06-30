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
        Schema::create('arabuluculuk_setting_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // İşlemi yapan
            $table->string('islem_turu'); // Ekleme, Silme, Düzenleme
            $table->text('detay'); // "X maddesi eklendi" gibi
            $table->string('ip_adresi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arabuluculuk_setting_logs');
    }
};
