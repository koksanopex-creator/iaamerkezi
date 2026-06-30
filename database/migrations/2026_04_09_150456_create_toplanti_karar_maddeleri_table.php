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
        Schema::create('toplanti_karar_maddeleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toplanti_id')->constrained('disiplin_kurulu_toplanti')->onDelete('cascade');
            $table->text('madde_metni');
            $table->foreignId('sorumlu_user_id')->nullable()->constrained('users');
            $table->enum('durum', ['beklemede', 'tamamlandı'])->default('beklemede');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toplanti_karar_maddeleri');
    }
};
