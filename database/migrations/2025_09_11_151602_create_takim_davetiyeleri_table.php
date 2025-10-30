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
        Schema::create('takim_davetiyeleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('takim_id')->constrained('takimlar')->onDelete('cascade');
            $table->foreignId('davet_eden_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('davet_edilen_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('durum', ['bekliyor', 'kabul edildi', 'reddedildi'])->default('bekliyor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('takim_davetiyeleri');
    }
};
