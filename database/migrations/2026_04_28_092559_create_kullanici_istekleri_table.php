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
        Schema::create('kullanici_istekleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('talep_turu'); // isim_degisikligi, bolum_degisikligi vs.
            $table->string('eski_deger')->nullable();
            $table->string('yeni_deger');
            $table->foreignId('yeni_bolum_id')->nullable()->constrained('bolumler')->onDelete('set null'); // if department change
            $table->string('durum')->default('bekliyor'); // bekliyor, onaylandi, reddedildi
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kullanici_istekleri');
    }
};
