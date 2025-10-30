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
        Schema::create('takimlar', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->foreignId('lider_user_id')->constrained('users');
            $table->text('amac')->nullable();
            $table->text('vizyon')->nullable();
            $table->text('misyon')->nullable();
            $table->text('kurallar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('takimlar');
    }
};
