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
        Schema::create('iaa_user', function (Blueprint $table) {
            $table->id();
            // Hangi Proje?
            $table->foreignId('iaa_id')->constrained('iaas')->onDelete('cascade');
            // Kim Görevli?
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Rolü (Lider, Üye vb.)
            $table->string('rol')->default('Üye'); 
            // Projeden kazanılan puan
            $table->decimal('kazanilan_puan', 8, 2)->default(0);
            
            $table->timestamps();
            
            // Aynı kişiyi aynı projeye 2 kere ekleyemesin
            $table->unique(['iaa_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_user');
    }
};
