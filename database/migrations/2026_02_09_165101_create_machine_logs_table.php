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
        Schema::create('machine_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_id')->nullable(); // Silinirse id kalmayabilir veya null tutulabilir
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bolum_id');
            $table->string('action'); // Ekleme, Güncelleme, Silme
            $table->text('details')->nullable(); // JSON veya detay metni
            $table->timestamps();

            // İlişkiler (Opsiyonel, foreign key kısıtlaması istenirse eklenebilir ama log olduğu için soft constraint daha iyi olabilir)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_logs');
    }
};
