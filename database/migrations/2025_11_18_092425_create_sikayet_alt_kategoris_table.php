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
        Schema::create('sikayet_alt_kategorileri', function (Blueprint $table) {
            $table->id();
            // Ana kategoriye bağlanır (Örn: "Kapak")
            $table->foreignId('sikayet_kategori_id')->constrained('sikayet_kategorileri')->onDelete('cascade');
            $table->string('ad'); // Örn: "Leke", "Bombe"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sikayet_alt_kategoris');
    }
};
