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
        Schema::create('takim_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('takim_id')->constrained('takimlar')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('gorev_tanimi')->nullable(); // Takım liderinin üye için yazdığı görev tanımı
            $table->timestamps();

            // Bir kullanıcının bir takıma sadece bir kez üye olabilmesini sağla
            $table->unique(['takim_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('takim_user');
    }
};
