<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disiplin_kurulu_uyelik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('rol', ['baskan', 'uye'])->default('uye');
            $table->date('katilim_tarihi');
            $table->date('ayrilma_tarihi')->nullable();
            $table->foreignId('ekleyen_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cikaran_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notlar')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disiplin_kurulu_uyelik');
    }
};
