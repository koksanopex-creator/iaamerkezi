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
        Schema::create('iaa_resimler', function (Blueprint $table) {
            $table->id();
            // Hangi İAA'ya ait olduğunu belirtmek için foreign key
            $table->foreignId('iaa_id')->constrained('iaas')->onDelete('cascade');
            $table->string('dosya_yolu'); // Resmin sunucudaki yolunu tutacak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_resimler');
    }
};
