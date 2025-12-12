<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->string('feedback_ip', 45)->nullable(); // IP Adresi
            $table->text('feedback_user_agent')->nullable(); // Tarayıcı Bilgisi
            $table->unsignedBigInteger('feedback_by_user_id')->nullable()->comment('Eğer personel oturumuyla yapıldıysa ID');
        });
    }

    public function down()
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            $table->dropColumn(['feedback_ip', 'feedback_user_agent', 'feedback_by_user_id']);
        });
    }
};
