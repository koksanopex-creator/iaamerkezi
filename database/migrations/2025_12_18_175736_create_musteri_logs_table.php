<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('musteri_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable(); // Hangi müşteriyle ilgili?
            $table->unsignedBigInteger('user_id')->nullable();     // İşlemi kim yaptı?
            $table->string('islem_turu'); // Ziyaret, Ekleme, Login vb.
            $table->text('aciklama')->nullable();
            $table->string('ip_adresi')->nullable();
            $table->timestamps();

            // Kullanıcı silinse de log kalsın, ama müşteri silinirse loglar da gidebilir (tercihe bağlı)
            // foreign key eklemiyoruz ki silinen verilerin logları hata vermesin.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('musteri_logs');
    }
};
