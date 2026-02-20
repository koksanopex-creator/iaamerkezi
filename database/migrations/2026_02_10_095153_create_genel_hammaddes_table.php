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
        Schema::create('genel_hammaddeler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bolum_id')->constrained('bolumler')->onDelete('cascade');
            $table->string('ad');
            $table->boolean('aktif_mi')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genel_hammaddeler');
    }
};
