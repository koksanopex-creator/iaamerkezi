<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disiplin_kurulu_toplanti', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            $table->text('aciklama')->nullable();
            $table->enum('tur', ['olaganüstü', 'olağan', 'uye_degisimi', 'karar_oturumu', 'diger'])->default('olağan');
            $table->dateTime('baslangic_tarihi');
            $table->dateTime('bitis_tarihi')->nullable();
            $table->string('yer')->nullable();
            $table->text('ajanda')->nullable();
            $table->text('toplanti_notu')->nullable();
            $table->enum('durum', ['planlandı', 'devam_ediyor', 'tamamlandı', 'iptal'])->default('planlandı');
            $table->foreignId('olusturan_user_id')->nullable()->constrained('users')->nullOnDelete();
            // İlgili disiplin dosyasına bağlantı (opsiyonel)
            $table->foreignId('disciplinary_case_id')->nullable()->constrained('disciplinary_cases')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disiplin_kurulu_toplanti');
    }
};
