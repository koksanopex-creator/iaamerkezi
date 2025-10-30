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
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Kayıtsız müşteri takibi için benzersiz token (nullable, çünkü kayıtlılar için boş olacak)
            $table->string('takip_token', 16)->unique()->nullable()->after('kazanilan_puan')->index(); // 16 karakterlik token varsaydım, index ekledim

            // Kayıtsız müşteri için hashlenmiş şifre (nullable)
            $table->string('guest_password_hash')->nullable()->after('takip_token');

            // Müşterinin çözüme verdiği geri bildirim (nullable)
            $table->string('musteri_feedback')->nullable()->after('guest_password_hash'); // Veya enum kullanabilirsin: ->enum('musteri_feedback', ['Onaylandı', 'Reddedildi', 'Revizyon İstendi'])->nullable()

            // Müşterinin geri bildirim notu (nullable)
            $table->text('musteri_feedback_note')->nullable()->after('musteri_feedback');

            // Şikayet düzenlemesinin kilitlendiği tarih (nullable)
            $table->timestamp('edit_locked_at')->nullable()->after('musteri_feedback_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musteri_sikayetleri', function (Blueprint $table) {
            // Sütunları kaldırırken index'i de belirtmek iyi bir pratiktir
            $table->dropIndex(['takip_token']); // Index'i kaldır
            $table->dropColumn([
                'takip_token',
                'guest_password_hash',
                'musteri_feedback',
                'musteri_feedback_note',
                'edit_locked_at'
            ]);
        });
    }
};