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
        Schema::create('iaa_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Şablon Adı, örn: "Standart 5 Adımlı İyileştirme"
            $table->text('description')->nullable(); // Şablon için açıklama
            $table->boolean('is_default')->default(false); // Varsayılan şablon mu?
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_workflows');
    }
};