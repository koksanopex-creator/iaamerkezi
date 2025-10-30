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
        Schema::create('sikayet_kategorileri', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            // Bu kategoriye ait bir şikayet geldiğinde, varsayılan olarak hangi takıma atanmalı?
            $table->foreignId('varsayilan_takim_id')->nullable()->constrained('takimlar')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sikayet_kategorileri');
    }
};
