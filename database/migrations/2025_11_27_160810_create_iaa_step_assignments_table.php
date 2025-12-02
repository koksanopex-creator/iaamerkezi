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
        Schema::create('iaa_step_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iaa_id')->constrained('iaas')->onDelete('cascade');
            $table->foreignId('iaa_workflow_step_id')->constrained('iaa_workflow_steps')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Sorumlu Kişi
            $table->foreignId('assigned_by')->constrained('users'); // Atayan (Lider)
            $table->timestamps();

            // Bir adımın aynı projede sadece tek bir sorumlusu olabilir
            $table->unique(['iaa_id', 'iaa_workflow_step_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_step_assignments');
    }
};
