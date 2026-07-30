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
        Schema::create('iaa_step_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iaa_id')->constrained('iaas')->onDelete('cascade');
            $table->foreignId('iaa_workflow_step_id')->constrained('iaa_workflow_steps')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tool_type'); // 'swot', '5why', 'chart' vs.
            $table->string('title')->nullable(); // Kullanıcı araca özel isim verebilir
            $table->json('data')->nullable(); // Aracın verileri burada tutulacak
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_step_tools');
    }
};
