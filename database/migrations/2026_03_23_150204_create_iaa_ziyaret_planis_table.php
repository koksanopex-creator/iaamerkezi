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
        Schema::create('iaa_ziyaret_planlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iaa_id')->constrained('iaas')->cascadeOnDelete();
            
            // Visitor / User
            $table->unsignedBigInteger('visitor_id')->nullable()->comment('Takvimdeki User ID');
            $table->string('visitor_name')->nullable()->comment('Eğer Diğer seçilmişse');
            
            // Fabrika
            $table->unsignedBigInteger('business_unit_id')->comment('Takvim Fabrika ID');
            
            // Ürün & Müşteri Detayı
            $table->unsignedBigInteger('customer_product_id')->nullable();
            $table->string('barcode')->nullable();
            $table->string('lot_no')->nullable();
            $table->json('contact_persons')->nullable();
            
            // Ziyaret Detayları
            $table->dateTime('visit_date');
            $table->string('visit_reason');
            $table->text('visit_notes')->nullable();
            
            // ONAY AKIŞI
            // Beklemede, Direktör Onayı Bekliyor, Yönetim Onayı Bekliyor, Revizyon Bekliyor, Onaylandı, Reddedildi, Tamamlandı
            $table->string('status')->default('Beklemede');
            $table->text('reject_reason')->nullable()->comment('Reddedilme veya Revizyon geçirme sebebi');
            
            // Ziyaret Tamamlanınca veya Onaydan Sonra
            $table->date('estimated_return_date')->nullable()->comment('Onay sonrası istenecek');
            $table->text('findings')->nullable();
            $table->text('result')->nullable();
            
            // Senkronizasyon
            $table->unsignedBigInteger('takvim_remote_id')->nullable()->comment('Takvimdeki CustomerVisit id');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iaa_ziyaret_planlari');
    }
};
