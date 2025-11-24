<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bu tablo, hangi kullanıcının hangi kategoriden sorumlu olduğunu tutacak
        Schema::create('bolum_kalite_yoneticileri', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sikayet_kategori_id'); // 'bolum_id' yerine kategori ID kullanıyoruz
            
            // İlişkiler
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sikayet_kategori_id')->references('id')->on('sikayet_kategorileri')->onDelete('cascade');

            // Bir kullanıcı bir kategorinin yöneticisi olarak sadece bir kez eklenebilir
            $table->unique(['user_id', 'sikayet_kategori_id'], 'user_kategori_unique');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bolum_kalite_yoneticileri');
    }
};