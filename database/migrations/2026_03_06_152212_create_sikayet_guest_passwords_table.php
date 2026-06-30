<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sikayet_guest_passwords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musteri_sikayeti_id')->constrained('musteri_sikayetleri')->cascadeOnDelete();
            $table->string('email');
            $table->string('recipient_name');
            $table->enum('recipient_type', ['firma_iletisim', 'yetkili', 'musteri_iletisim']);
            $table->string('password_hash');
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('musteri_sikayeti_id');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sikayet_guest_passwords');
    }
};
