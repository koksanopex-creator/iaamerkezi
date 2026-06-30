<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplinary_case_id')->constrained('disciplinary_cases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // İtirazı yapan kişi
            $table->text('reason'); // İtiraz gerekçesi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_appeals');
    }
};
