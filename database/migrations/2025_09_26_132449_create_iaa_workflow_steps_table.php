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
        Schema::create('iaa_workflow_steps', function (Blueprint $table) {
            $table->id();
            // Bir şablon silinirse, ona ait adımlar da silinsin (onDelete('cascade'))
            $table->foreignId('iaa_workflow_id')->constrained('iaa_workflows')->onDelete('cascade');
            $table->string('name'); // Adım Adı, örn: "Kök Neden Analizi"
            $table->text('description')->nullable(); // Adım için rehber metin
            $table->unsignedInteger('order'); // Adımları sıralamak için (1, 2, 3...)
            $table->unsignedInteger('default_duration_days')->default(7); // Adım için varsayılan gün süresi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_workflow_steps');
    }
};