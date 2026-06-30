<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('arabulucu_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // İşlemi yapan
            $table->unsignedBigInteger('arabulucu_id')->nullable(); // Etkilenen (Silinirse null olabilir)
            $table->string('islem_turu'); // EKLEME, GÜNCELLEME, SİLME, PASİF/AKTİF
            $table->text('detay')->nullable(); // Log notu
            $table->string('ip_adres')->nullable(); // Güvenlik için IP
            $table->timestamps();

            // İlişkiler
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // arabulucu_id için foreign key koymuyoruz, arabulucu silinse de log kalsın.
        });
    }

    public function down()
    {
        Schema::dropIfExists('arabulucu_logs');
    }
};