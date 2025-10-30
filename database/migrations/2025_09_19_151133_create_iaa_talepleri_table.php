<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iaa_talepleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iaa_id')->constrained('iaas')->onDelete('cascade');
            $table->foreignId('takim_id')->constrained('takimlar')->onDelete('cascade');
            $table->foreignId('talep_eden_user_id')->constrained('users')->onDelete('cascade'); // Talebi yapan liderin ID'si
            $table->string('durum')->default('beklemede'); // beklemede, onaylandi, reddedildi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iaa_talepleri');
    }
};