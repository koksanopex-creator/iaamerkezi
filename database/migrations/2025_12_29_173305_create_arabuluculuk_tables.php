<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ARABULUCULAR
        Schema::create('arabulucular', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sicil_no')->nullable();
            $table->string('sehir')->nullable();
            $table->text('adres')->nullable();
            $table->string('email')->nullable();
            $table->string('telefon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. DOSYALAR (ANA TABLO)
        Schema::create('arabuluculuk_cases', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['ihtiyari', 'zorunlu']);
            $table->string('status')->default('taslak');
            $table->enum('mutabakat', ['beklemede', 'anlasildi', 'anlasilmadi'])->default('beklemede');
            $table->string('dosya_no')->nullable();
            
            // İLGİLİ KİŞİLER
            $table->unsignedBigInteger('calisan_user_id')->nullable();
            $table->unsignedBigInteger('arabulucu_id')->nullable();
            $table->unsignedBigInteger('internal_lawyer_id')->nullable();
            $table->unsignedBigInteger('external_lawyer_id')->nullable();
            
            $table->enum('owner_role', ['personel', 'hukuk'])->default('personel');
            
            $table->decimal('talep_tutari', 12, 2)->nullable();
            $table->decimal('anlasilan_tutar', 12, 2)->nullable();
            
            $table->boolean('board_required')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('calisan_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('arabulucu_id')->references('id')->on('arabulucular')->onDelete('set null');
            $table->foreign('internal_lawyer_id')->references('id')->on('users');
            $table->foreign('external_lawyer_id')->references('id')->on('users');
        });

        // 3. ÖDEMELER
        Schema::create('arabuluculuk_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('arabuluculuk_cases')->onDelete('cascade');
            $table->decimal('tutar', 12, 2)->nullable();
            $table->string('odenecek_kisi')->nullable();
            $table->enum('odeme_durumu', ['bekliyor', 'odendi'])->default('bekliyor');
            $table->date('odeme_tarihi')->nullable();
            $table->unsignedBigInteger('finance_onay_by')->nullable();
            $table->dateTime('finance_onay_at')->nullable();
            $table->timestamps();
        });

        // 4. DOSYALAR (Files)
        Schema::create('arabuluculuk_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('arabuluculuk_cases')->onDelete('cascade');
            $table->string('doc_type');
            $table->text('dosya_yolu');
            $table->string('orijinal_adi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });

        // 5. LOGLAR
        Schema::create('arabuluculuk_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('arabuluculuk_cases')->onDelete('cascade');
            $table->foreignId('user_id')->nullable();
            $table->string('islem');
            $table->text('detay')->nullable();
            $table->timestamps();
        });

        // 6. KURUL DEĞERLENDİRMELERİ (HATA VEREN EKSİK TABLO BU)
        // Standart bir değerlendirme yapısı kurdum, ihtiyacına göre sütun ekleyebilirsin.
        Schema::create('arabuluculuk_kurul_degerlendirmeleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('arabuluculuk_cases')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Değerlendiren Kişi
            $table->text('yorum')->nullable();
            $table->string('karar')->nullable(); // Onay, Red vb.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arabuluculuk_kurul_degerlendirmeleri');
        Schema::dropIfExists('arabuluculuk_logs');
        Schema::dropIfExists('arabuluculuk_files');
        Schema::dropIfExists('arabuluculuk_payments');
        Schema::dropIfExists('arabuluculuk_cases');
        Schema::dropIfExists('arabulucular');
    }
};