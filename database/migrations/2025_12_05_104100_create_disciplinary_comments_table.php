<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('disciplinary_cases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('yorum');
            $table->json('dosyalar')->nullable(); // Dosya ekleri (Opsiyonel)
            $table->timestamps();
            $table->softDeletes(); // Silinen yorumları loglamak için
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disciplinary_comments');
    }
};