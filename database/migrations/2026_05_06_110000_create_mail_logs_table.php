<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();

            // Polymorphic ilişki (MusteriSikayeti, DisciplinaryCase, Iaa vb.)
            $table->nullableMorphs('loggable');

            // Kaynak bilgileri
            $table->string('source_page', 500)->nullable()->comment('İşlemin yapıldığı URL');
            $table->string('source_action', 255)->comment('Kısa açıklama: Yeni Şikayet Kaydı vb.');

            // Alıcı ve hata bilgileri
            $table->json('recipients')->nullable()->comment('Kimlere gönderilmeye çalışıldı');
            $table->text('error_message')->comment('SMTP hata mesajı');

            // Tekrar dene için gerekli veriler
            $table->string('notification_class', 500)->nullable()->comment('Bildirim sınıfının tam adı');
            $table->json('notification_data')->nullable()->comment('Bildirim parametreleri');

            // Durum takibi
            $table->unsignedTinyInteger('retry_count')->default(0)->comment('Kaç kez denendiği');
            $table->timestamp('resolved_at')->nullable()->comment('Çözüldü tarihi');
            $table->unsignedBigInteger('resolved_by')->nullable()->comment('Çözümü sağlayan kullanıcı');

            // Bölüm bazlı filtreleme için
            $table->unsignedBigInteger('bolum_id')->nullable()->comment('İlgili bölüm ID (scoping için)');

            $table->timestamps();

            // Indexler
            $table->index('bolum_id');
            $table->index('resolved_at');
            $table->index('created_at');

            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('bolum_id')->references('id')->on('bolumler')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
