<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iaa_progress_updates', function (Blueprint $table) {
            $table->id();
            // Artık doğru tablo olan 'iaa_talepleri' tablosuna bağlanıyoruz
            $table->foreignId('iaa_talep_id')->constrained('iaa_talepleri')->onDelete('cascade');
            $table->foreignId('iaa_workflow_step_id')->constrained('iaa_workflow_steps')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->longText('content')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iaa_progress_updates');
    }
};