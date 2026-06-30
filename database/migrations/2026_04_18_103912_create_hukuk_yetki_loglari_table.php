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
        Schema::create('hukuk_yetki_loglari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // 'role', 'permission'
            $table->string('target_name'); // 'Hukuk Yöneticisi', 'disiplin.ayarlar.gor' vb.
            $table->string('action'); // 'atandi', 'kaldirildi'
            $table->text('details')->nullable(); 
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hukuk_yetki_loglari');
    }
};
